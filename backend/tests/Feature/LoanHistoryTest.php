<?php

namespace Tests\Feature;

use App\Enums\MaterialStatus;
use App\Enums\Role;
use App\Models\Category;
use App\Models\Loan;
use App\Models\Material;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanHistoryTest extends TestCase
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

    private function makeLoan(array $override = []): Loan
    {
        return Loan::factory()->createOne(array_merge([
            'user_id'     => $this->teacher->id,
            'material_id' => $this->material->id,
        ], $override));
    }

    // ── INDEX ─────────────────────────────────────────────────

    public function test_admin_can_list_all_loans(): void
    {
        $this->makeLoan();
        $this->makeLoan();

        $this->actingAs($this->admin, 'sanctum')
             ->getJson('/api/v1/loans')
             ->assertStatus(200)
             ->assertJsonStructure(['data', 'meta'])
             ->assertJsonPath('meta.total', 2);
    }

    public function test_teacher_cannot_list_loans(): void
    {
        $this->actingAs($this->teacher, 'sanctum')
             ->getJson('/api/v1/loans')
             ->assertStatus(403);
    }

    public function test_filter_ongoing_loans(): void
    {
        Loan::factory()->ongoing()->createOne([
            'user_id'     => $this->teacher->id,
            'material_id' => $this->material->id,
        ]);
        Loan::factory()->returned()->createOne([
            'user_id'     => $this->teacher->id,
            'material_id' => $this->material->id,
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
             ->getJson('/api/v1/loans?ongoing=true')
             ->assertStatus(200);

        collect($response->json('data'))->each(fn($l) =>
            $this->assertNull($l['actual_return_date'])
        );
    }

    public function test_filter_overdue_loans(): void
    {
        Loan::factory()->overdue()->createOne([
            'user_id'     => $this->teacher->id,
            'material_id' => $this->material->id,
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
             ->getJson('/api/v1/loans?overdue=true')
             ->assertStatus(200);

        $this->assertGreaterThan(0, $response->json('meta.total'));
    }

    public function test_filter_returned_loans(): void
    {
        Loan::factory()->returned()->createOne([
            'user_id'     => $this->teacher->id,
            'material_id' => $this->material->id,
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
             ->getJson('/api/v1/loans?returned=true')
             ->assertStatus(200);

        collect($response->json('data'))->each(fn($l) =>
            $this->assertNotNull($l['actual_return_date'])
        );
    }

    public function test_filter_by_date_range(): void
    {
        Loan::factory()->createOne([
            'user_id'              => $this->teacher->id,
            'material_id'          => $this->material->id,
            'loan_date'            => '2025-01-10',
            'expected_return_date' => '2025-01-20',
        ]);

        $this->actingAs($this->admin, 'sanctum')
             ->getJson('/api/v1/loans?from=2025-01-01&to=2025-01-31')
             ->assertStatus(200);
    }

    // ── SHOW ──────────────────────────────────────────────────

    public function test_admin_can_show_loan_details(): void
    {
        $loan = $this->makeLoan();

        $this->actingAs($this->admin, 'sanctum')
             ->getJson("/api/v1/loans/{$loan->id}")
             ->assertStatus(200)
             ->assertJsonStructure([
                 'data' => [
                     'id', 'loan_date', 'expected_return_date',
                     'is_returned', 'is_overdue', 'is_ongoing',
                     'overdue_days', 'user', 'material',
                 ]
             ]);
    }

    public function test_show_returns_404_for_unknown(): void
    {
        $this->actingAs($this->admin, 'sanctum')
             ->getJson('/api/v1/loans/nonexistent-uuid')
             ->assertStatus(404);
    }

    // ── MATERIAL HISTORY ─────────────────────────────────────

    public function test_admin_can_view_material_loan_history(): void
    {
        $this->makeLoan();
        $this->makeLoan();

        $this->actingAs($this->admin, 'sanctum')
             ->getJson("/api/v1/materials/{$this->material->id}/loans")
             ->assertStatus(200)
             ->assertJsonStructure(['data', 'meta']);
    }

    public function test_material_history_returns_404_for_unknown_material(): void
    {
        $this->actingAs($this->admin, 'sanctum')
             ->getJson('/api/v1/materials/nonexistent-uuid/loans')
             ->assertStatus(404);
    }

    public function test_material_history_only_returns_loans_for_that_material(): void
    {
        $otherMaterial = Material::factory()->createOne(['category_id' => $this->category->id]);

        $this->makeLoan(); // for $this->material
        Loan::factory()->createOne([
            'user_id'     => $this->teacher->id,
            'material_id' => $otherMaterial->id,
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
             ->getJson("/api/v1/materials/{$this->material->id}/loans")
             ->assertStatus(200);

        collect($response->json('data'))->each(fn($l) =>
            $this->assertEquals($this->material->id, $l['material']['id'])
        );
    }

    // ── TEACHER HISTORY ───────────────────────────────────────

    public function test_admin_can_view_teacher_loan_history(): void
    {
        $this->makeLoan();
        $this->makeLoan();

        $this->actingAs($this->admin, 'sanctum')
             ->getJson("/api/v1/teachers/{$this->teacher->id}/loans")
             ->assertStatus(200)
             ->assertJsonStructure(['data', 'meta']);
    }

    public function test_teacher_history_returns_404_for_unknown_teacher(): void
    {
        $this->actingAs($this->admin, 'sanctum')
             ->getJson('/api/v1/teachers/nonexistent-uuid/loans')
             ->assertStatus(404);
    }

    public function test_teacher_history_only_returns_their_loans(): void
    {
        $otherTeacher = User::factory()->createOne(['role' => Role::Teacher]);
        $this->makeLoan();
        Loan::factory()->createOne([
            'user_id'     => $otherTeacher->id,
            'material_id' => $this->material->id,
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
             ->getJson("/api/v1/teachers/{$this->teacher->id}/loans")
             ->assertStatus(200);

        collect($response->json('data'))->each(fn($l) =>
            $this->assertEquals($this->teacher->id, $l['user']['id'])
        );
    }

    // ── IMMUTABILITY ──────────────────────────────────────────

    public function test_cannot_update_loan_record(): void
    {
        $loan = $this->makeLoan();

        $this->actingAs($this->admin, 'sanctum')
             ->putJson("/api/v1/loans/{$loan->id}", ['notes' => 'Modifié'])
             ->assertStatus(405);
    }

    public function test_cannot_delete_loan_record(): void
    {
        $loan = $this->makeLoan();

        $this->actingAs($this->admin, 'sanctum')
             ->deleteJson("/api/v1/loans/{$loan->id}")
             ->assertStatus(405);
    }

    // ── LOAN UTILITY METHODS ──────────────────────────────────

    public function test_ongoing_loan_detected_correctly(): void
    {
        $loan = Loan::factory()->ongoing()->makeOne();
        $this->assertTrue($loan->isOngoing());
        $this->assertFalse($loan->isReturned());
        $this->assertFalse($loan->isOverdue());
        $this->assertEquals(0, $loan->overdueDays());
    }

    public function test_overdue_loan_detected_correctly(): void
    {
        $loan = Loan::factory()->overdue()->makeOne();
        $this->assertTrue($loan->isOverdue());
        $this->assertFalse($loan->isOngoing());
        $this->assertFalse($loan->isReturned());
        $this->assertGreaterThan(0, $loan->overdueDays());
    }

    public function test_returned_loan_detected_correctly(): void
    {
        $loan = Loan::factory()->returned()->makeOne();
        $this->assertTrue($loan->isReturned());
        $this->assertFalse($loan->isOngoing());
        $this->assertFalse($loan->isOverdue());
    }
}
