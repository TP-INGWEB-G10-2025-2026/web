<?php

namespace Tests\Feature;

use App\Enums\MaterialStatus;
use App\Enums\ReservationStatus;
use App\Enums\Role;
use App\Models\Category;
use App\Models\Loan;
use App\Models\Material;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatisticsTest extends TestCase
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

        $this->category = Category::factory()->create(['name' => 'Informatique']);
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

    // ── USAGE STATS ───────────────────────────────────────────

    public function test_admin_can_get_usage_stats(): void
    {
        $this->makeLoan();

        $this->actingAs($this->admin, 'sanctum')
             ->getJson('/api/v1/statistics/usage')
             ->assertStatus(200)
             ->assertJsonStructure([
                 'data' => [
                     'summary',
                     'top_materials',
                     'category_usage',
                     'monthly_loans',
                     'currently_on_loan',
                     'overdue_loans',
                     'reservation_summary',
                 ],
                 'generated',
                 'period',
             ]);
    }

    public function test_usage_stats_accepts_year_filter(): void
    {
        $this->actingAs($this->admin, 'sanctum')
             ->getJson('/api/v1/statistics/usage?year=2025')
             ->assertStatus(200)
             ->assertJsonPath('period.year', 2025);
    }

    public function test_usage_stats_rejects_invalid_year(): void
    {
        $this->actingAs($this->admin, 'sanctum')
             ->getJson('/api/v1/statistics/usage?year=1990')
             ->assertStatus(422);
    }

    public function test_teacher_cannot_access_statistics(): void
    {
        $this->actingAs($this->teacher, 'sanctum')
             ->getJson('/api/v1/statistics/usage')
             ->assertStatus(403);
    }

    // ── SUMMARY ───────────────────────────────────────────────

    public function test_summary_returns_correct_counts(): void
    {
        $this->makeLoan();
        Reservation::factory()->createOne([
            'user_id'    => $this->teacher->id,
            'status'     => ReservationStatus::Pending->value,
            'start_date' => now()->addDays(2)->format('Y-m-d'),
            'end_date'   => now()->addDays(5)->format('Y-m-d'),
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
             ->getJson('/api/v1/statistics/summary')
             ->assertStatus(200);

        $data = $response->json('data');
        $this->assertArrayHasKey('total_loans', $data);
        $this->assertArrayHasKey('total_materials', $data);
        $this->assertArrayHasKey('total_categories', $data);
        $this->assertArrayHasKey('pending_reservations', $data);
        $this->assertEquals(1, $data['total_loans']);
        $this->assertEquals(1, $data['pending_reservations']);
    }

    // ── TOP MATERIALS ─────────────────────────────────────────

    public function test_top_materials_returns_correct_structure(): void
    {
        $this->makeLoan();
        $this->makeLoan();

        $response = $this->actingAs($this->admin, 'sanctum')
             ->getJson('/api/v1/statistics/materials/top')
             ->assertStatus(200)
             ->assertJsonStructure([
                 'data' => [['id', 'name', 'total_loans', 'avg_loan_days', 'category_name']],
                 'meta' => ['limit', 'count'],
             ]);

        $this->assertGreaterThan(0, $response->json('data.0.total_loans'));
    }

    public function test_top_materials_respects_limit(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
             ->getJson('/api/v1/statistics/materials/top?limit=3')
             ->assertStatus(200);

        $this->assertLessThanOrEqual(3, count($response->json('data')));
        $this->assertEquals(3, $response->json('meta.limit'));
    }

    public function test_top_materials_rejects_invalid_limit(): void
    {
        $this->actingAs($this->admin, 'sanctum')
             ->getJson('/api/v1/statistics/materials/top?limit=100')
             ->assertStatus(422);
    }

    // ── MONTHLY LOANS ─────────────────────────────────────────

    public function test_monthly_loans_returns_12_months(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
             ->getJson('/api/v1/statistics/loans/monthly')
             ->assertStatus(200);

        $this->assertCount(12, $response->json('data'));
    }

    public function test_monthly_loans_returns_correct_year(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
             ->getJson('/api/v1/statistics/loans/monthly?year=2024')
             ->assertStatus(200);

        $this->assertEquals(2024, $response->json('meta.year'));

        collect($response->json('data'))->each(fn($m) =>
            $this->assertEquals(2024, $m['year'])
        );
    }

    public function test_monthly_loans_counts_correctly(): void
    {
        $currentMonth = now()->month;
        $currentYear  = now()->year;

        Loan::factory()->count(3)->createOne([
            'user_id'              => $this->teacher->id,
            'material_id'          => $this->material->id,
            'loan_date'            => now()->format('Y-m-d'),
            'expected_return_date' => now()->addDays(7)->format('Y-m-d'),
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
             ->getJson("/api/v1/statistics/loans/monthly?year={$currentYear}")
             ->assertStatus(200);

        $monthData = collect($response->json('data'))
            ->firstWhere('month', $currentMonth);

        $this->assertGreaterThan(0, $monthData['total']);
    }

    // ── OVERDUE ───────────────────────────────────────────────

    public function test_overdue_loans_endpoint(): void
    {
        Loan::factory()->overdue()->createOne([
            'user_id'     => $this->teacher->id,
            'material_id' => $this->material->id,
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
             ->getJson('/api/v1/statistics/overdue')
             ->assertStatus(200)
             ->assertJsonStructure([
                 'data' => [['loan_id', 'material', 'user', 'overdue_days', 'expected_return_date']],
                 'meta' => ['count'],
             ]);

        $this->assertGreaterThan(0, $response->json('meta.count'));
        $this->assertGreaterThan(0, $response->json('data.0.overdue_days'));
    }

    // ── CATEGORY USAGE ────────────────────────────────────────

    public function test_category_usage_in_full_stats(): void
    {
        $this->makeLoan();

        $response = $this->actingAs($this->admin, 'sanctum')
             ->getJson('/api/v1/statistics/usage')
             ->assertStatus(200);

        $categoryData = $response->json('data.category_usage');
        $this->assertNotEmpty($categoryData);
        $this->assertArrayHasKey('name', $categoryData[0]);
        $this->assertArrayHasKey('total_materials', $categoryData[0]);
        $this->assertArrayHasKey('usage_rate_percent', $categoryData[0]);
    }

    // ── ACCESS CONTROL ────────────────────────────────────────

    public function test_unauthenticated_cannot_access_statistics(): void
    {
        $this->getJson('/api/v1/statistics/usage')->assertStatus(401);
        $this->getJson('/api/v1/statistics/materials/top')->assertStatus(401);
        $this->getJson('/api/v1/statistics/loans/monthly')->assertStatus(401);
        $this->getJson('/api/v1/statistics/overdue')->assertStatus(401);
        $this->getJson('/api/v1/statistics/summary')->assertStatus(401);
    }
}
