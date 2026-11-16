<?php

namespace Tests\Feature;

use App\Models\DigitalIdentity;
use App\Models\User;
use App\Services\IdentityHasher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class IdentityCorrectionFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['blockchain.enabled' => false]);
    }

    private function identityFor(User $owner, string $status = 'rejected'): DigitalIdentity
    {
        return DigitalIdentity::create([
            'user_id' => $owner->id,
            'did' => 'did:trustwall:'.Str::lower((string) Str::ulid()),
            'legal_name' => 'Incorrect Name',
            'identity_type' => 'Student ID',
            'identity_number_hash' => app(IdentityHasher::class)->hash('OLD-123'),
            'status' => $status,
            'rejection_reason' => $status === 'rejected' ? 'Correct the name and ID number.' : null,
            'submitted_at' => now(),
        ]);
    }

    public function test_owner_can_open_correction_page_after_rejection(): void
    {
        $owner = User::factory()->create();
        $identity = $this->identityFor($owner);

        $this->actingAs($owner)->get(route('identity.edit', $identity))
            ->assertOk()
            ->assertSee('Correct Your Identity Details')
            ->assertSee('Correct the name and ID number.');
    }

    public function test_other_user_cannot_edit_or_resubmit_someone_elses_identity(): void
    {
        $identity = $this->identityFor(User::factory()->create());
        $this->actingAs(User::factory()->create())
            ->get(route('identity.edit', $identity))->assertForbidden();

        $this->patch(route('identity.resubmit', $identity), [
            'legal_name' => 'Imposter',
            'identity_type' => 'Student ID',
        ])->assertForbidden();
    }

    public function test_pending_identity_cannot_enter_correction_flow(): void
    {
        $owner = User::factory()->create();
        $identity = $this->identityFor($owner, 'pending');

        $this->actingAs($owner)->get(route('identity.edit', $identity))->assertStatus(422);
        $this->patch(route('identity.resubmit', $identity), [
            'legal_name' => 'Edited Name',
            'identity_type' => 'Student ID',
        ])->assertStatus(422);
    }

    public function test_owner_can_correct_rejected_details_and_resubmit(): void
    {
        $owner = User::factory()->create();
        $identity = $this->identityFor($owner);
        $originalHash = $identity->identity_number_hash;

        $this->actingAs($owner)->patch(route('identity.resubmit', $identity), [
            'legal_name' => 'Corrected Name',
            'identity_type' => 'National ID',
            'identity_number' => 'NEW-123',
        ])->assertRedirect(route('identity.index'));

        $identity->refresh();
        $this->assertSame('pending', $identity->status);
        $this->assertSame('Corrected Name', $identity->legal_name);
        $this->assertSame('National ID', $identity->identity_type);
        $this->assertNull($identity->rejection_reason);
        $this->assertNotSame($originalHash, $identity->identity_number_hash);
        $this->assertSame(app(IdentityHasher::class)->hash('NEW-123'), $identity->identity_number_hash);
        $this->assertSame('identity.resubmitted', $identity->ledgerEntries()->firstOrFail()->action);
    }

    public function test_replacing_document_updates_hash_and_private_file(): void
    {
        Storage::fake('local');
        $owner = User::factory()->create();
        $identity = $this->identityFor($owner);
        $previousPath = 'identity-documents/'.$owner->id.'/previous.pdf';
        Storage::disk('local')->put($previousPath, 'old file');
        $identity->update(['document_path' => $previousPath, 'document_hash' => hash('sha256', 'old file')]);

        $this->actingAs($owner)->patch(route('identity.resubmit', $identity), [
            'legal_name' => $identity->legal_name,
            'identity_type' => $identity->identity_type,
            'document_file' => UploadedFile::fake()->createWithContent('corrected.pdf', 'new file'),
        ])->assertRedirect(route('identity.index'));

        $identity->refresh();
        $this->assertSame(hash('sha256', 'new file'), $identity->document_hash);
        Storage::disk('local')->assertMissing($previousPath);
        Storage::disk('local')->assertExists($identity->document_path);
    }

    public function test_public_verifier_does_not_claim_unverified_identity_is_approved(): void
    {
        $identity = $this->identityFor(User::factory()->create(), 'pending');

        $this->get(route('verify.index', ['q' => $identity->did]))
            ->assertOk()
            ->assertSee('Awaiting administrator review')
            ->assertDontSee('Identity approved');
    }
}
