<?php

namespace Tests\Feature;

use App\Models\DigitalIdentity;
use App\Models\User;
use App\Services\IdentityLedger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class MissingIdentityDocumentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['blockchain.enabled' => false]);
        Storage::fake('local');
    }

    public function test_new_identity_requires_a_supporting_document(): void
    {
        $owner = User::factory()->create();

        $this->actingAs($owner)
            ->post(route('identity.store'), [
                'legal_name' => 'Test Applicant',
                'identity_type' => 'Student ID',
                'identity_number' => 'SAMPLE-100',
            ])
            ->assertSessionHasErrors('document_file');

        $this->assertDatabaseCount('digital_identities', 0);
    }

    public function test_pending_identity_without_document_can_attach_one(): void
    {
        $owner = User::factory()->create();
        $identity = $this->pendingIdentity($owner);

        $this->actingAs($owner)
            ->get(route('identity.index'))
            ->assertOk()
            ->assertSee('Upload document');

        $this->post(route('identity.document.attach', $identity), [
            'document_file' => UploadedFile::fake()->createWithContent('my-passport.pdf', 'sample document'),
        ])->assertRedirect(route('identity.index'));

        $identity->refresh();
        $this->assertSame('pending', $identity->status);
        $this->assertSame('my-passport.pdf', $identity->document_original_name);
        $this->assertSame(hash('sha256', 'sample document'), $identity->document_hash);
        Storage::disk('local')->assertExists($identity->document_path);
        $this->assertSame('identity.document_attached', $identity->ledgerEntries()->firstOrFail()->action);
        $this->assertTrue(app(IdentityLedger::class)->inspect($identity)['valid']);

        $this->get(route('identity.index'))
            ->assertOk()
            ->assertSee('Download private document')
            ->assertDontSee('Upload document');
    }

    public function test_other_users_cannot_attach_documents_to_an_identity(): void
    {
        $identity = $this->pendingIdentity(User::factory()->create());

        $this->actingAs(User::factory()->create())
            ->post(route('identity.document.attach', $identity), [
                'document_file' => UploadedFile::fake()->createWithContent('file.pdf', 'unauthorized'),
            ])
            ->assertForbidden();

        $this->assertNull($identity->fresh()->document_path);
    }

    public function test_admin_cannot_attach_a_document_for_the_applicant(): void
    {
        $identity = $this->pendingIdentity(User::factory()->create());

        $this->actingAs(User::factory()->create(['is_admin' => true]))
            ->post(route('identity.document.attach', $identity), [
                'document_file' => UploadedFile::fake()->createWithContent('file.pdf', 'unauthorized'),
            ])
            ->assertForbidden();
    }

    public function test_approved_identity_cannot_attach_a_new_document(): void
    {
        $owner = User::factory()->create();
        $identity = $this->pendingIdentity($owner);
        $identity->update(['status' => 'verified']);

        $this->actingAs($owner)
            ->post(route('identity.document.attach', $identity), [
                'document_file' => UploadedFile::fake()->createWithContent('file.pdf', 'too late'),
            ])
            ->assertStatus(422);
    }

    public function test_pending_identity_cannot_overwrite_already_attached_document(): void
    {
        $owner = User::factory()->create();
        $identity = $this->pendingIdentity($owner);
        $identity->update([
            'document_hash' => str_repeat('a', 64),
            'document_path' => 'identity-documents/example.pdf',
        ]);

        $this->actingAs($owner)
            ->post(route('identity.document.attach', $identity), [
                'document_file' => UploadedFile::fake()->createWithContent('file.pdf', 'replacement'),
            ])
            ->assertStatus(422);
    }

    private function pendingIdentity(User $owner): DigitalIdentity
    {
        $identity = DigitalIdentity::create([
            'user_id' => $owner->id,
            'did' => 'did:trustwall:'.Str::lower((string) Str::ulid()),
            'legal_name' => 'Applicant Name',
            'identity_type' => 'Student ID',
            'identity_number_hash' => str_repeat('b', 64),
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        app(IdentityLedger::class)->record($identity, 'identity.submitted', $owner, [
            'document_uploaded' => false,
        ]);

        return $identity;
    }
}
