<?php

namespace Tests\Feature;

use App\Models\DigitalIdentity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SimplifiedIdentityExperienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_user_sees_identity_creation_action(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Create Digital Identity')
            ->assertSee('Not created')
            ->assertDontSee('On-chain anchors');
    }

    public function test_create_page_uses_clear_fields_without_wallet_requirement(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('identity.create'))
            ->assertOk()
            ->assertSee('Create Your Digital Identity')
            ->assertSee('Submit for Verification')
            ->assertDontSee('name="wallet_address"', false)
            ->assertDontSee('name="public_key"', false);
    }

    public function test_pending_identity_has_clear_status_and_did(): void
    {
        $user = User::factory()->create();

        DigitalIdentity::create([
            'user_id' => $user->id,
            'did' => 'did:trustwall:examplepending',
            'legal_name' => 'Example Student',
            'identity_type' => 'Student ID',
            'identity_number_hash' => str_repeat('a', 64),
            'status' => 'pending',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Pending verification')
            ->assertSee('did:trustwall:examplepending')
            ->assertDontSee('Create Digital Identity');
    }
}
