<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'role'     => Role::Teacher,
            'password' => bcrypt('OldPass@123'),
        ]);
    }

    public function test_authenticated_user_can_view_profile(): void
    {
        $this->actingAs($this->user, 'sanctum')
             ->getJson('/api/v1/profile')
             ->assertStatus(200)
             ->assertJsonPath('user.email', $this->user->email);
    }

    public function test_unauthenticated_cannot_view_profile(): void
    {
        $this->getJson('/api/v1/profile')
             ->assertStatus(401);
    }

    public function test_user_can_update_name_and_phone(): void
    {
        $this->actingAs($this->user, 'sanctum')
             ->putJson('/api/v1/profile', [
                 'name'  => 'Updated Name',
                 'phone' => '+33612345678',
             ])
             ->assertStatus(200)
             ->assertJsonPath('user.name', 'Updated Name')
             ->assertJsonPath('user.phone', '+33612345678');
    }

    public function test_user_can_change_password_with_correct_current_password(): void
    {
        $this->actingAs($this->user, 'sanctum')
             ->putJson('/api/v1/profile', [
                 'current_password'          => 'OldPass@123',
                 'new_password'              => 'NewPass@456',
                 'new_password_confirmation' => 'NewPass@456',
             ])
             ->assertStatus(200);
    }

    public function test_password_change_fails_with_wrong_current_password(): void
    {
        $this->actingAs($this->user, 'sanctum')
             ->putJson('/api/v1/profile', [
                 'current_password'          => 'wrongpassword',
                 'new_password'              => 'NewPass@456',
                 'new_password_confirmation' => 'NewPass@456',
             ])
             ->assertStatus(422)
             ->assertJsonStructure(['errors' => ['current_password']]);
    }

    public function test_password_change_fails_when_confirmation_mismatch(): void
    {
        $this->actingAs($this->user, 'sanctum')
             ->putJson('/api/v1/profile', [
                 'current_password'          => 'OldPass@123',
                 'new_password'              => 'NewPass@456',
                 'new_password_confirmation' => 'DifferentPass@789',
             ])
             ->assertStatus(422);
    }

    public function test_email_update_fails_if_already_taken(): void
    {
        User::factory()->create(['email' => 'taken@test.com']);

        $this->actingAs($this->user, 'sanctum')
             ->putJson('/api/v1/profile', ['email' => 'taken@test.com'])
             ->assertStatus(422)
             ->assertJsonStructure(['errors' => ['email']]);
    }
}
