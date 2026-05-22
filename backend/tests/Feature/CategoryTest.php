<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Category;
use App\Models\Material;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $teacher;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var User $admin */
        $admin = User::factory()->createOne(['role' => Role::Admin]);
        $this->admin = $admin;

        /** @var User $teacher */
        $teacher = User::factory()->createOne(['role' => Role::Teacher]);
        $this->teacher = $teacher;
    }

    public function test_admin_can_list_categories(): void
    {
        Category::factory()->count(3)->create();

        $this->actingAs($this->admin, 'sanctum')
             ->getJson('/api/v1/categories')
             ->assertStatus(200)
             ->assertJsonStructure(['data']);
    }

    public function test_teacher_cannot_access_categories(): void
    {
        $this->actingAs($this->teacher, 'sanctum')
             ->getJson('/api/v1/categories')
             ->assertStatus(403);
    }

    public function test_admin_can_create_category(): void
    {
        $this->actingAs($this->admin, 'sanctum')
             ->postJson('/api/v1/categories', ['name' => 'Informatique'])
             ->assertStatus(201)
             ->assertJsonPath('data.name', 'Informatique');
    }

    public function test_create_category_fails_with_duplicate_name(): void
    {
        Category::factory()->create(['name' => 'Informatique']);

        $this->actingAs($this->admin, 'sanctum')
             ->postJson('/api/v1/categories', ['name' => 'Informatique'])
             ->assertStatus(422)
             ->assertJsonStructure(['errors' => ['name']]);
    }

    public function test_create_category_fails_without_name(): void
    {
        $this->actingAs($this->admin, 'sanctum')
             ->postJson('/api/v1/categories', [])
             ->assertStatus(422)
             ->assertJsonStructure(['errors' => ['name']]);
    }

    public function test_admin_can_show_category_with_materials(): void
    {
        $category = Category::factory()->create();
        Material::factory()->count(2)->create(['category_id' => $category->id]);

        $this->actingAs($this->admin, 'sanctum')
             ->getJson("/api/v1/categories/{$category->id}")
             ->assertStatus(200)
             ->assertJsonPath('data.name', $category->name)
             ->assertJsonStructure(['data' => ['materials']]);
    }

    public function test_show_returns_404_for_unknown_category(): void
    {
        $this->actingAs($this->admin, 'sanctum')
             ->getJson('/api/v1/categories/nonexistent-uuid')
             ->assertStatus(404);
    }

    public function test_admin_can_update_category(): void
    {
        $category = Category::factory()->create(['name' => 'Ancien nom']);

        $this->actingAs($this->admin, 'sanctum')
             ->putJson("/api/v1/categories/{$category->id}", ['name' => 'Nouveau nom'])
             ->assertStatus(200)
             ->assertJsonPath('data.name', 'Nouveau nom');
    }

    public function test_update_category_fails_with_duplicate_name(): void
    {
        Category::factory()->create(['name' => 'Pris']);
        $category = Category::factory()->create(['name' => 'Libre']);

        $this->actingAs($this->admin, 'sanctum')
             ->putJson("/api/v1/categories/{$category->id}", ['name' => 'Pris'])
             ->assertStatus(422);
    }

    public function test_admin_can_delete_empty_category(): void
    {
        $category = Category::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
             ->deleteJson("/api/v1/categories/{$category->id}")
             ->assertStatus(200)
             ->assertJsonPath('message', 'Catégorie supprimée avec succès.');

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_cannot_delete_category_with_linked_materials(): void
    {
        $category = Category::factory()->create();
        Material::factory()->create(['category_id' => $category->id]);

        $this->actingAs($this->admin, 'sanctum')
             ->deleteJson("/api/v1/categories/{$category->id}")
             ->assertStatus(422)
             ->assertJsonStructure(['errors' => ['category']]);
    }
}
