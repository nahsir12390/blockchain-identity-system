<?php

namespace Tests\Feature;

use App\Models\DigitalIdentity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_visit_the_dashboard(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('dashboard'));
        $response->assertOk();
    }

    public function test_regular_user_sees_identity_enrollment_and_not_admin_statistics(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Create Digital Identity')
            ->assertDontSee('Pending Verification Requests')
            ->assertDontSee('Total Identities');
    }

    public function test_admin_sees_review_dashboard_instead_of_create_identity(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $owner = User::factory()->create(['is_admin' => false]);

        $identity = DigitalIdentity::create([
            'user_id' => $owner->id,
            'did' => 'did:trustwall:'.Str::lower((string) Str::ulid()),
            'legal_name' => 'Student Test',
            'identity_type' => 'Student ID',
            'identity_number_hash' => str_repeat('a', 64),
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Pending Verification Requests')
            ->assertSee('Total Identities')
            ->assertSee('Verified Identities')
            ->assertSee('Rejected Identities')
            ->assertSee('Student Test')
            ->assertSee(route('admin.identities.show', $identity))
            ->assertDontSee('Create Digital Identity')
            ->assertDontSee('My Identity');
    }

    public function test_admin_cannot_open_or_submit_identity_creation(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));

        $this->get(route('identity.create'))->assertForbidden();

        $this->post(route('identity.store'), [
            'legal_name' => 'System Admin',
            'identity_type' => 'Student ID',
            'identity_number' => 'ADMIN-123',
        ])->assertForbidden();

        $this->assertDatabaseCount('digital_identities', 0);
    }
}
