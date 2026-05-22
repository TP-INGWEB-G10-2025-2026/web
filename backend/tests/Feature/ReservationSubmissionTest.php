<?php

namespace Tests\Feature;

use App\Enums\MaterialStatus;
use App\Enums\ReservationStatus;
use App\Enums\Role;
use App\Events\ReservationSubmitted;
use App\Models\Category;
use App\Models\Material;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ReservationSubmissionTest extends TestCase
{
    use RefreshDatabase;

    private User     $teacher;
    private User     $admin;
    private Category $category;
    private Material $material;

    protected function setUp(): void
    {
        parent::setUp();
        Event::fake();


        /** @var User $teacher */
        $teacher = User::factory()->createOne(['role' => Role::Teacher]);
        $this->teacher = $teacher;

        /** @var User $admin */
        $admin = User::factory()->createOne(['role' => Role::Admin]);
        $this->admin = $admin;

        $this->category = Category::factory()->create();
        $this->material = Material::factory()->create([
            'category_id' => $this->category->id,
            'status'      => MaterialStatus::Available->value,
        ]);
    }

    // ── STORE ─────────────────────────────────────────────────

    public function test_teacher_can_submit_reservation(): void
    {
        Event::fake();
        Mail::fake();

        $this->actingAs($this->teacher, 'sanctum')
             ->postJson('/api/v1/reservations', [
                 'start_date' => now()->addDays(2)->format('Y-m-d'),
                 'end_date'   => now()->addDays(5)->format('Y-m-d'),
             ])
             ->assertStatus(201)
             ->assertJsonPath('data.status', 'pending')
             ->assertJsonStructure(['message', 'data' => ['id', 'status', 'start_date', 'end_date']]);
    }

    public function test_reservation_is_created_with_pending_status(): void
    {
        Event::fake();

        $this->actingAs($this->teacher, 'sanctum')
             ->postJson('/api/v1/reservations', [
                 'start_date' => now()->addDays(1)->format('Y-m-d'),
                 'end_date'   => now()->addDays(4)->format('Y-m-d'),
             ])
             ->assertStatus(201);

        $this->assertDatabaseHas('reservations', [
            'user_id' => $this->teacher->id,
            'status'  => ReservationStatus::Pending->value,
        ]);
    }

    public function test_blocked_user_cannot_submit_reservation(): void
    {
        $this->teacher->update(['is_blocked' => true]);

        $this->actingAs($this->teacher, 'sanctum')
             ->postJson('/api/v1/reservations', [
                 'start_date' => now()->addDays(2)->format('Y-m-d'),
                 'end_date'   => now()->addDays(5)->format('Y-m-d'),
             ])
             ->assertStatus(403);
    }

    public function test_reservation_fails_with_past_start_date(): void
    {
        $this->actingAs($this->teacher, 'sanctum')
             ->postJson('/api/v1/reservations', [
                 'start_date' => now()->subDays(1)->format('Y-m-d'),
                 'end_date'   => now()->addDays(3)->format('Y-m-d'),
             ])
             ->assertStatus(422)
             ->assertJsonStructure(['errors' => ['start_date']]);
    }

    public function test_reservation_fails_when_end_before_start(): void
    {
        $this->actingAs($this->teacher, 'sanctum')
             ->postJson('/api/v1/reservations', [
                 'start_date' => now()->addDays(5)->format('Y-m-d'),
                 'end_date'   => now()->addDays(2)->format('Y-m-d'),
             ])
             ->assertStatus(422)
             ->assertJsonStructure(['errors' => ['end_date']]);
    }

    public function test_reservation_fails_with_missing_dates(): void
    {
        $this->actingAs($this->teacher, 'sanctum')
             ->postJson('/api/v1/reservations', [])
             ->assertStatus(422)
             ->assertJsonStructure(['errors' => ['start_date', 'end_date']]);
    }

    public function test_unauthenticated_cannot_submit_reservation(): void
    {
        $this->postJson('/api/v1/reservations', [
            'start_date' => now()->addDays(2)->format('Y-m-d'),
            'end_date'   => now()->addDays(5)->format('Y-m-d'),
        ])->assertStatus(401);
    }

    public function test_event_is_fired_on_submission(): void
    {
        Event::fake();

        $this->actingAs($this->teacher, 'sanctum')
             ->postJson('/api/v1/reservations', [
                 'start_date' => now()->addDays(2)->format('Y-m-d'),
                 'end_date'   => now()->addDays(5)->format('Y-m-d'),
             ])
             ->assertStatus(201);

        Event::assertDispatched(ReservationSubmitted::class);
    }

    // ── AVAILABILITY ──────────────────────────────────────────

    public function test_can_check_available_materials(): void
    {
        $this->actingAs($this->teacher, 'sanctum')
             ->getJson('/api/v1/reservations/available?start='
                 . now()->addDays(10)->format('Y-m-d')
                 . '&end='
                 . now()->addDays(15)->format('Y-m-d'))
             ->assertStatus(200)
             ->assertJsonStructure(['data', 'meta' => ['start_date', 'end_date', 'count']]);
    }

    public function test_unavailable_material_not_returned_when_booked(): void
    {
        Event::fake();

        // Create a validated reservation on our material
        Reservation::factory()->create([
            'user_id'     => $this->teacher->id,
            'material_id' => $this->material->id,
            'status'      => ReservationStatus::Validated->value,
            'start_date'  => now()->addDays(5)->format('Y-m-d'),
            'end_date'    => now()->addDays(10)->format('Y-m-d'),
        ]);

        $response = $this->actingAs($this->teacher, 'sanctum')
             ->getJson('/api/v1/reservations/available?start='
                 . now()->addDays(6)->format('Y-m-d')
                 . '&end='
                 . now()->addDays(9)->format('Y-m-d'))
             ->assertStatus(200);

        $ids = collect($response->json('data'))->pluck('id')->toArray();
        $this->assertNotContains($this->material->id, $ids);
    }

    public function test_available_check_fails_without_dates(): void
    {
        $this->actingAs($this->teacher, 'sanctum')
             ->getJson('/api/v1/reservations/available')
             ->assertStatus(422);
    }

    public function test_cancelled_reservation_does_not_block_material(): void
    {
        // Cancelled reservation — material should still appear as available
        Reservation::factory()->create([
            'user_id'     => $this->teacher->id,
            'material_id' => $this->material->id,
            'status'      => ReservationStatus::Cancelled->value,
            'start_date'  => now()->addDays(5)->format('Y-m-d'),
            'end_date'    => now()->addDays(10)->format('Y-m-d'),
        ]);

        $response = $this->actingAs($this->teacher, 'sanctum')
             ->getJson('/api/v1/reservations/available?start='
                 . now()->addDays(5)->format('Y-m-d')
                 . '&end='
                 . now()->addDays(10)->format('Y-m-d'))
             ->assertStatus(200);

        $ids = collect($response->json('data'))->pluck('id')->toArray();
        $this->assertContains($this->material->id, $ids);
    }
}
