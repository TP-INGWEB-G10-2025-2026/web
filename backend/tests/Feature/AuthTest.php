<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_login_with_valid_credentials(): void
    {
        /** @var User $user */
        $user = User::factory()->createOne([
            'email'    => 'admin@test.com',
            'password' => bcrypt('Admin@12345'),
            'role'     => Role::Admin,
        ]);

        $this->postJson('/api/v1/auth/login', [
            'email'    => 'admin@test.com',
            'password' => 'Admin@12345',
        ])->assertStatus(200)
          ->assertJsonStructure(['message', 'token', 'user'])
          ->assertJsonPath('user.email', 'admin@test.com');
    }

    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->createOne(['email' => 'admin@test.com', 'role' => Role::Admin]);

        $this->postJson('/api/v1/auth/login', [
            'email'    => 'admin@test.com',
            'password' => 'wrongpassword',
        ])->assertStatus(422);
    }

    public function test_login_fails_with_missing_fields(): void
    {
        $this->postJson('/api/v1/auth/login', [])
             ->assertStatus(422)
             ->assertJsonStructure(['message', 'errors']);
    }

    public function test_blocked_user_cannot_login(): void
    {
        User::factory()->createOne([
            'email'      => 'blocked@test.com',
            'password'   => bcrypt('Password@123'),
            'role'       => Role::Teacher,
            'is_blocked' => true,
        ]);

        $this->postJson('/api/v1/auth/login', [
            'email'    => 'blocked@test.com',
            'password' => 'Password@123',
        ])->assertStatus(403);
    }

    public function test_me_returns_authenticated_user(): void
    {
        /** @var User $user */
        $user = User::factory()->createOne(['role' => Role::Admin]);

        $this->actingAs($user, 'sanctum')
             ->getJson('/api/v1/auth/me')
             ->assertStatus(200)
             ->assertJsonPath('user.email', $user->email);
    }

    public function test_me_returns_401_without_token(): void
    {
        $this->getJson('/api/v1/auth/me')
             ->assertStatus(401);
    }

    public function test_user_can_logout(): void
    {
        /** @var User $user */
        $user = User::factory()->createOne(['role' => Role::Admin]);

        $this->actingAs($user, 'sanctum')
             ->postJson('/api/v1/auth/logout')
             ->assertStatus(200)
             ->assertJsonPath('message', 'Déconnexion réussie.');
    }
}
