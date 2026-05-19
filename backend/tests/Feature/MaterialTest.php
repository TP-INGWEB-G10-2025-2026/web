<?php

namespace Tests\Feature;

use App\Enums\MaterialStatus;
use App\Role;
use App\Models\Category;
use App\Models\Material;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaterialTest extends TestCase
{
    use RefreshDatabase;

    private User     $admin;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var User $admin */
        $admin = User::factory()->createOne(['role' => Role::Admin]);
        $this->admin = $admin;

        $this->category = Category::factory()->create(['name' => 'Informatique']);
    }

    private function payload(array $override = []): array
    {
        return array_merge([
            'name'        => 'Ordinateur portable',
            'category_id' => $this->category->id,
            'status'      => 'available',
            'description' => 'Un bon laptop.',
        ], $override);
    }

    // ── INDEX ────────────────────────────────────────────────

    public function test_admin_can_list_materials(): void
    {
        Material::factory()->count(3)->create(['category_id' => $this->category->id]);

        $this->actingAs($this->admin, 'sanctum')
             ->getJson('/api/v1/materials')
             ->assertStatus(200)
             ->assertJsonStructure(['data', 'meta']);
    }

    public function test_can_filter_materials_by_name(): void
    {
        Material::factory()->create(['name' => 'Projecteur HD', 'category_id' => $this->category->id]);
        Material::factory()->create(['name' => 'Clavier mécanique', 'category_id' => $this->category->id]);

        $response = $this->actingAs($this->admin, 'sanctum')
                         ->getJson('/api/v1/materials?name=Projecteur')
                         ->assertStatus(200);

        $this->assertStringContainsString('Projecteur', json_encode($response->json('data')));
    }

    public function test_can_filter_materials_by_status(): void
    {
        Material::factory()->create(['category_id' => $this->category->id, 'status' => 'available']);
        Material::factory()->create(['category_id' => $this->category->id, 'status' => 'broken']);

        $response = $this->actingAs($this->admin, 'sanctum')
                         ->getJson('/api/v1/materials?status=broken')
                         ->assertStatus(200);

        collect($response->json('data'))->each(fn($m) =>
            $this->assertEquals('broken', $m['status'])
        );
    }

    public function test_can_filter_materials_by_category(): void
    {
        $other = Category::factory()->create(['name' => 'Audiovisuel']);
        Material::factory()->create(['category_id' => $this->category->id]);
        Material::factory()->create(['category_id' => $other->id]);

        $response = $this->actingAs($this->admin, 'sanctum')
                         ->getJson("/api/v1/materials?category_id={$this->category->id}")
                         ->assertStatus(200);

        collect($response->json('data'))->each(fn($m) =>
            $this->assertEquals($this->category->id, $m['category_id'])
        );
    }

    // ── SHOW ─────────────────────────────────────────────────

    public function test_admin_can_show_material(): void
    {
        $material = Material::factory()->create(['category_id' => $this->category->id]);

        $this->actingAs($this->admin, 'sanctum')
             ->getJson("/api/v1/materials/{$material->id}")
             ->assertStatus(200)
             ->assertJsonPath('data.id', $material->id)
             ->assertJsonStructure(['data' => ['category']]);
    }

    public function test_show_returns_404_for_unknown_material(): void
    {
        $this->actingAs($this->admin, 'sanctum')
             ->getJson('/api/v1/materials/nonexistent-uuid')
             ->assertStatus(404);
    }

    // ── STORE ────────────────────────────────────────────────

    public function test_admin_can_create_material(): void
    {
        $this->actingAs($this->admin, 'sanctum')
             ->postJson('/api/v1/materials', $this->payload())
             ->assertStatus(201)
             ->assertJsonPath('data.name', 'Ordinateur portable')
             ->assertJsonPath('data.status', 'available');
    }

    public function test_create_fails_without_required_fields(): void
    {
        $this->actingAs($this->admin, 'sanctum')
             ->postJson('/api/v1/materials', [])
             ->assertStatus(422)
             ->assertJsonStructure(['errors' => ['name', 'category_id', 'status']]);
    }

    public function test_create_fails_with_invalid_category(): void
    {
        $this->actingAs($this->admin, 'sanctum')
             ->postJson('/api/v1/materials', $this->payload(['category_id' => 'nonexistent-uuid']))
             ->assertStatus(422)
             ->assertJsonStructure(['errors' => ['category_id']]);
    }

    public function test_create_fails_with_invalid_status(): void
    {
        $this->actingAs($this->admin, 'sanctum')
             ->postJson('/api/v1/materials', $this->payload(['status' => 'flying']))
             ->assertStatus(422)
             ->assertJsonStructure(['errors' => ['status']]);
    }

    // ── UPDATE ───────────────────────────────────────────────

    public function test_admin_can_update_material(): void
    {
        $material = Material::factory()->create(['category_id' => $this->category->id]);

        $this->actingAs($this->admin, 'sanctum')
             ->putJson("/api/v1/materials/{$material->id}", ['name' => 'Nouveau nom'])
             ->assertStatus(200)
             ->assertJsonPath('data.name', 'Nouveau nom');
    }

    // ── STATUS ───────────────────────────────────────────────

    public function test_admin_can_update_status(): void
    {
        $material = Material::factory()->create([
            'category_id' => $this->category->id,
            'status'      => 'available',
        ]);

        $this->actingAs($this->admin, 'sanctum')
             ->patchJson("/api/v1/materials/{$material->id}/status", ['status' => 'in_use'])
             ->assertStatus(200)
             ->assertJsonPath('data.status', 'in_use');
    }

    public function test_status_update_fails_with_invalid_value(): void
    {
        $material = Material::factory()->create(['category_id' => $this->category->id]);

        $this->actingAs($this->admin, 'sanctum')
             ->patchJson("/api/v1/materials/{$material->id}/status", ['status' => 'unknown'])
             ->assertStatus(422)
             ->assertJsonStructure(['errors' => ['status']]);
    }

    // ── DELETE ───────────────────────────────────────────────

    public function test_admin_can_soft_delete_material(): void
    {
        $material = Material::factory()->create([
            'category_id' => $this->category->id,
            'status'      => 'available',
        ]);

        $this->actingAs($this->admin, 'sanctum')
             ->deleteJson("/api/v1/materials/{$material->id}")
             ->assertStatus(200)
             ->assertJsonPath('message', 'Matériel supprimé avec succès.');

        $this->assertSoftDeleted('materials', ['id' => $material->id]);
    }

    public function test_cannot_delete_material_in_use(): void
    {
        $material = Material::factory()->create([
            'category_id' => $this->category->id,
            'status'      => 'in_use',
        ]);

        $this->actingAs($this->admin, 'sanctum')
             ->deleteJson("/api/v1/materials/{$material->id}")
             ->assertStatus(422)
             ->assertJsonStructure(['errors' => ['material']]);
    }

    public function test_deleted_material_returns_404(): void
    {
        $material = Material::factory()->create(['category_id' => $this->category->id]);
        $material->delete();

        $this->actingAs($this->admin, 'sanctum')
             ->getJson("/api/v1/materials/{$material->id}")
             ->assertStatus(404);
    }

    public function test_unauthenticated_cannot_access_materials(): void
    {
        $this->getJson('/api/v1/materials')->assertStatus(401);
    }
}
