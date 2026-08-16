<?php

namespace Tests\Feature;

use App\Models\DigitalIdentity;
use App\Models\User;
use App\Services\IdentityHasher;
use App\Services\IdentityLedger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_identity_hasher_uses_keyed_normalized_hashes(): void
    {
        $hasher = app(IdentityHasher::class);

        $firstHash = $hasher->hash(' ABC-123 ');
        $secondHash = $hasher->hash('abc-123');

        $this->assertSame($firstHash, $secondHash);
        $this->assertNotSame(hash('sha256', 'abc-123'), $firstHash);
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $firstHash);
    }

    public function test_non_admin_users_cannot_access_admin_routes(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get(route('admin.users.index'))->assertForbidden();
    }

    public function test_admin_users_can_access_admin_routes(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));

        $this->get(route('admin.users.index'))->assertOk();
    }

    public function test_invalid_identity_status_transition_is_rejected(): void
    {
        config(['blockchain.enabled' => false]);

        $admin = User::factory()->create(['is_admin' => true]);
        $owner = User::factory()->create();
        $identity = $this->identityFor($owner, ['status' => 'rejected']);

        $this->actingAs($admin)
            ->patch(route('admin.identities.verify', $identity))
            ->assertStatus(422);

        $this->assertSame('rejected', $identity->fresh()->status);
    }

    public function test_ledger_entries_can_be_recomputed_and_detect_tampering(): void
    {
        $user = User::factory()->create();
        $identity = $this->identityFor($user);

        $entry = app(IdentityLedger::class)->record($identity, 'identity.submitted', $user, [
            'identity_type' => 'National ID',
            'wallet_linked' => false,
        ]);

        $this->assertTrue(app(IdentityLedger::class)->inspect($identity)['valid']);

        $entry->update(['payload_hash' => str_repeat('b', 64)]);

        $inspection = app(IdentityLedger::class)->inspect($identity->fresh());

        $this->assertFalse($inspection['valid']);
        $this->assertContains('Block 1 payload hash has been altered.', $inspection['issues']);
    }

    public function test_identity_document_upload_is_stored_privately_and_hashed(): void
    {
        config(['blockchain.enabled' => false]);
        Storage::fake('local');

        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('identity.store'), [
                'legal_name' => 'Document Owner',
                'identity_type' => 'National ID',
                'identity_number' => 'ABC-12345',
                'wallet_address' => '0x123',
                'public_key' => 'public-key',
                'document_file' => UploadedFile::fake()->createWithContent('national-id.pdf', 'private identity document'),
            ])
            ->assertRedirect(route('identity.index'));

        $identity = DigitalIdentity::firstOrFail();

        $this->assertNotNull($identity->document_path);
        $this->assertSame('national-id.pdf', $identity->document_original_name);
        $this->assertSame(hash('sha256', 'private identity document'), $identity->document_hash);
        Storage::disk('local')->assertExists($identity->document_path);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function identityFor(User $user, array $attributes = []): DigitalIdentity
    {
        return DigitalIdentity::create(array_merge([
            'user_id' => $user->id,
            'did' => 'did:trustwall:'.Str::lower((string) Str::ulid()),
            'legal_name' => 'Test Identity',
            'identity_type' => 'National ID',
            'identity_number_hash' => str_repeat('a', 64),
            'status' => 'pending',
            'submitted_at' => now(),
        ], $attributes));
    }
}
