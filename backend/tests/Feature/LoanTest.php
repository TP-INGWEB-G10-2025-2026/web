<?php

namespace Tests\Feature;

use App\Enums\MaterialStatus;
use App\Enums\ReturnStatus;
use App\Enums\Role;
use App\Models\Category;
use App\Models\Loan;
use App\Models\Material;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class LoanTest extends TestCase
{
    use RefreshDatabase;

    private User     $admin;
    private User     $teacher;
    private Category $category;
    private Material $material;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var User $admin */
        $admin = User::factory()->createOne(['role' => Role::Admin]);
        $this->admin = $admin;

        /** @var User $teacher */
        $teacher = User::factory()->createOne(['role' => Role::Teacher]);
        $this->teacher = $teacher;

        $this->category = Category::factory()->create();
        $this->material = Material::factory()->createOne([
            'category_id' => $this->category->id,
            'status'      => MaterialStatus::Available->value,
        ]);
    }

    private function payload(array $override = []): array
    {
        return array_merge([
            'user_id'              => $this->teacher->id,
            'material_id'          => $this->material->id,
            'loan_date'            => today()->format('Y-m-d'),
            'expected_return_date' => today()->addDays(7)->format('Y-m-d'),
        ], $override);
    }

    // ── INDEX ─────────────────────────────────────────────────

    public function test_admin_can_list_loans(): void
    {
        Loan::factory()->count(3)->create([
            'user_id'     => $this->teacher->id,
            'material_id' => $this->material->id,
        ]);

        $this->actingAs($this->admin, 'sanctum')
             ->getJson('/api/v1/loans')
             ->assertStatus(200)
             ->assertJsonStructure(['data', 'meta']);
    }

    public function test_teacher_cannot_list_loans(): void
    {
        $this->actingAs($this->teacher, 'sanctum')
             ->getJson('/api/v1/loans')
             ->assertStatus(403);
    }

    // ── STORE ─────────────────────────────────────────────────

    public function test_admin_can_create_loan(): void
    {
        $this->actingAs($this->admin, 'sanctum')
             ->postJson('/api/v1/loans', $this->payload())
             ->assertStatus(201)
             ->assertJsonPath('data.is_returned', false)
             ->assertJsonPath('data.is_ongoing', true);

        // Material should now be in_use
        $this->assertDatabaseHas('materials', [
            'id'     => $this->material->id,
            'status' => MaterialStatus::InUse->value,
        ]);
    }

    public function test_cannot_loan_unavailable_material(): void
    {
        $this->material->update(['status' => MaterialStatus::InUse->value]);

        $this->actingAs($this->admin, 'sanctum')
             ->postJson('/api/v1/loans', $this->payload())
             ->assertStatus(422)
             ->assertJsonStructure(['errors' => ['material_id']]);
    }

    public function test_cannot_loan_broken_material(): void
    {
        $this->material->update(['status' => MaterialStatus::Broken->value]);

        $this->actingAs($this->admin, 'sanctum')
             ->postJson('/api/v1/loans', $this->payload())
             ->assertStatus(422);
    }

    public function test_create_loan_fails_missing_fields(): void
    {
        $this->actingAs($this->admin, 'sanctum')
             ->postJson('/api/v1/loans', [])
             ->assertStatus(422)
             ->assertJsonStructure(['errors' => ['user_id', 'material_id', 'loan_date', 'expected_return_date']]);
    }

    public function test_create_fails_if_return_date_before_loan_date(): void
    {
        $this->actingAs($this->admin, 'sanctum')
             ->postJson('/api/v1/loans', $this->payload([
                 'loan_date'            => today()->addDays(5)->format('Y-m-d'),
                 'expected_return_date' => today()->format('Y-m-d'),
             ]))
             ->assertStatus(422)
             ->assertJsonStructure(['errors' => ['expected_return_date']]);
    }

    // ── RETURN ────────────────────────────────────────────────

    public function test_admin_can_return_loan_in_good_condition(): void
    {
        $loan = Loan::factory()->ongoing()->createOne([
            'user_id'     => $this->teacher->id,
            'material_id' => $this->material->id,
        ]);
        $this->material->update(['status' => MaterialStatus::InUse->value]);

        $this->actingAs($this->admin, 'sanctum')
             ->patchJson("/api/v1/loans/{$loan->id}/return", [
                 'return_status' => 'good',
             ])
             ->assertStatus(200)
             ->assertJsonPath('data.is_returned', true)
             ->assertJsonPath('data.return_status', 'good');

        // Material should be available again
        $this->assertDatabaseHas('materials', [
            'id'     => $this->material->id,
            'status' => MaterialStatus::Available->value,
        ]);
    }

    public function test_return_damaged_sets_material_broken(): void
    {
        Mail::fake();

        $loan = Loan::factory()->ongoing()->createOne([
            'user_id'     => $this->teacher->id,
            'material_id' => $this->material->id,
        ]);
        $this->material->update(['status' => MaterialStatus::InUse->value]);

        $this->actingAs($this->admin, 'sanctum')
             ->patchJson("/api/v1/loans/{$loan->id}/return", [
                 'return_status' => 'damaged',
                 'notes'         => 'Écran cassé.',
             ])
             ->assertStatus(200)
             ->assertJsonPath('data.return_status', 'damaged');

        $this->assertDatabaseHas('materials', [
            'id'     => $this->material->id,
            'status' => MaterialStatus::Broken->value,
        ]);
    }

    public function test_return_lost_sets_material_broken_and_alerts_admin(): void
    {
        Mail::fake();

        $loan = Loan::factory()->ongoing()->createOne([
            'user_id'     => $this->teacher->id,
            'material_id' => $this->material->id,
        ]);
        $this->material->update(['status' => MaterialStatus::InUse->value]);

        $this->actingAs($this->admin, 'sanctum')
             ->patchJson("/api/v1/loans/{$loan->id}/return", [
                 'return_status' => 'lost',
                 'notes'         => 'Introuvable.',
             ])
             ->assertStatus(200)
             ->assertJsonPath('data.return_status', 'lost');

        $this->assertDatabaseHas('materials', [
            'id'     => $this->material->id,
            'status' => MaterialStatus::Broken->value,
        ]);

        Mail::assertQueued(\App\Mail\DamagedMaterialMail::class);
    }

    public function test_cannot_return_already_returned_loan(): void
    {
        $loan = Loan::factory()->returned()->createOne([
            'user_id'     => $this->teacher->id,
            'material_id' => $this->material->id,
        ]);

        $this->actingAs($this->admin, 'sanctum')
             ->patchJson("/api/v1/loans/{$loan->id}/return", [
                 'return_status' => 'good',
             ])
             ->assertStatus(422);
    }

    public function test_return_fails_with_invalid_status(): void
    {
        $loan = Loan::factory()->ongoing()->createOne([
            'user_id'     => $this->teacher->id,
            'material_id' => $this->material->id,
        ]);

        $this->actingAs($this->admin, 'sanctum')
             ->patchJson("/api/v1/loans/{$loan->id}/return", [
                 'return_status' => 'unknown',
             ])
             ->assertStatus(422);
    }

    public function test_overdue_loan_detection(): void
    {
        $loan = Loan::factory()->overdue()->createOne([
            'user_id'     => $this->teacher->id,
            'material_id' => $this->material->id,
        ]);

        $this->assertTrue($loan->isOverdue());
        $this->assertFalse($loan->isOngoing());
        $this->assertFalse($loan->isReturned());
        $this->assertGreaterThan(0, $loan->overdueDays());
    }

    public function test_unauthenticated_cannot_access_loans(): void
    {
        $this->getJson('/api/v1/loans')->assertStatus(401);
    }
}
