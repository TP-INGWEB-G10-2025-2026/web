<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $teacher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role'     => Role::Admin,
            'password' => bcrypt('Admin@12345'),
        ]);

        $this->teacher = User::factory()->create([
            'role'     => Role::Teacher,
            'password' => bcrypt('Teacher@123'),
        ]);
    }

    // ── INDEX ────────────────────────────────────────────────

    public function test_admin_can_list_teachers(): void
    {
        User::factory()->count(3)->create(['role' => Role::Teacher]);

        $this->actingAs($this->admin, 'sanctum')
             ->getJson('/api/v1/teachers')
             ->assertStatus(200)
             ->assertJsonStructure(['data', 'meta']);
    }

    public function test_teacher_cannot_list_teachers(): void
    {
        $this->actingAs($this->teacher, 'sanctum')
             ->getJson('/api/v1/teachers')
             ->assertStatus(403);
    }

    public function test_unauthenticated_cannot_list_teachers(): void
    {
        $this->getJson('/api/v1/teachers')
             ->assertStatus(401);
    }

    public function test_admin_can_filter_teachers_by_name(): void
    {
        User::factory()->create(['name' => 'Jean Dupont', 'role' => Role::Teacher]);
        User::factory()->create(['name' => 'Marie Curie', 'role' => Role::Teacher]);

        $response = $this->actingAs($this->admin, 'sanctum')
                         ->getJson('/api/v1/teachers?name=Jean')
                         ->assertStatus(200);

        $this->assertStringContainsString('Jean', json_encode($response->json('data')));
    }

    // ── SHOW ─────────────────────────────────────────────────

    public function test_admin_can_show_teacher(): void
    {
        $this->actingAs($this->admin, 'sanctum')
             ->getJson("/api/v1/teachers/{$this->teacher->id}")
             ->assertStatus(200)
             ->assertJsonPath('data.email', $this->teacher->email);
    }

    public function test_show_returns_404_for_unknown_teacher(): void
    {
        $this->actingAs($this->admin, 'sanctum')
             ->getJson('/api/v1/teachers/nonexistent-uuid')
             ->assertStatus(404);
    }

    // ── STORE ────────────────────────────────────────────────

    public function test_admin_can_create_teacher(): void
    {
        $this->actingAs($this->admin, 'sanctum')
             ->postJson('/api/v1/teachers', [
                 'name'     => 'New Teacher',
                 'email'    => 'newteacher@test.com',
                 'password' => 'Teacher@123',
                 'phone'    => '+33612345678',
             ])
             ->assertStatus(201)
             ->assertJsonPath('data.email', 'newteacher@test.com')
             ->assertJsonPath('data.role', 'teacher');
    }

    public function test_create_teacher_fails_with_duplicate_email(): void
    {
        $this->actingAs($this->admin, 'sanctum')
             ->postJson('/api/v1/teachers', [
                 'name'     => 'Duplicate',
                 'email'    => $this->teacher->email,
                 'password' => 'Teacher@123',
             ])
             ->assertStatus(422)
             ->assertJsonStructure(['errors' => ['email']]);
    }

    public function test_create_teacher_fails_with_missing_fields(): void
    {
        $this->actingAs($this->admin, 'sanctum')
             ->postJson('/api/v1/teachers', ['name' => 'Only Name'])
             ->assertStatus(422)
             ->assertJsonStructure(['errors' => ['email', 'password']]);
    }

    // ── UPDATE ───────────────────────────────────────────────

    public function test_admin_can_update_teacher(): void
    {
        $this->actingAs($this->admin, 'sanctum')
             ->putJson("/api/v1/teachers/{$this->teacher->id}", [
                 'name'  => 'Updated Name',
                 'phone' => '+33699999999',
             ])
             ->assertStatus(200)
             ->assertJsonPath('data.name', 'Updated Name');
    }

    // ── BLOCK / UNBLOCK ──────────────────────────────────────

    public function test_admin_can_block_teacher(): void
    {
        $this->actingAs($this->admin, 'sanctum')
             ->patchJson("/api/v1/teachers/{$this->teacher->id}/block")
             ->assertStatus(200)
             ->assertJsonPath('data.is_blocked', true);
    }

    public function test_admin_can_unblock_teacher(): void
    {
        $this->teacher->update(['is_blocked' => true]);

        $this->actingAs($this->admin, 'sanctum')
             ->patchJson("/api/v1/teachers/{$this->teacher->id}/unblock")
             ->assertStatus(200)
             ->assertJsonPath('data.is_blocked', false);
    }

    public function test_cannot_block_admin_account(): void
    {
        $this->actingAs($this->admin, 'sanctum')
             ->patchJson("/api/v1/teachers/{$this->admin->id}/block")
             ->assertStatus(422);
    }

    public function test_blocked_teacher_cannot_access_api(): void
    {
        $this->teacher->update(['is_blocked' => true]);

        $this->actingAs($this->teacher, 'sanctum')
             ->getJson('/api/v1/profile')
             ->assertStatus(403);
    }

    // ── DELETE ───────────────────────────────────────────────

    public function test_admin_can_soft_delete_teacher(): void
    {
        $this->actingAs($this->admin, 'sanctum')
             ->deleteJson("/api/v1/teachers/{$this->teacher->id}")
             ->assertStatus(200)
             ->assertJsonPath('message', 'Enseignant supprimé avec succès.');

        $this->assertSoftDeleted('users', ['id' => $this->teacher->id]);
    }

    public function test_cannot_delete_admin_account(): void
    {
        $this->actingAs($this->admin, 'sanctum')
             ->deleteJson("/api/v1/teachers/{$this->admin->id}")
             ->assertStatus(422);
    }
}
