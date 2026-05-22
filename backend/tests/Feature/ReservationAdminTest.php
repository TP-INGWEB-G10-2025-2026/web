<?php

namespace Tests\Feature;

use App\Enums\MaterialStatus;
use App\Enums\ReservationStatus;
use App\Enums\Role;
use App\Models\Category;
use App\Models\Material;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ReservationAdminTest extends TestCase
{
    use RefreshDatabase;

    private User        $admin;
    private User        $teacher;
    private Category    $category;
    private Material    $material;
    private Reservation $reservation;

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

        $this->reservation = Reservation::factory()->createOne([
            'user_id'    => $this->teacher->id,
            'status'     => ReservationStatus::Pending->value,
            'start_date' => now()->addDays(2)->format('Y-m-d'),
            'end_date'   => now()->addDays(5)->format('Y-m-d'),
        ]);
    }

    // ── INDEX ─────────────────────────────────────────────────

    public function test_admin_can_list_reservations(): void
    {
        $this->actingAs($this->admin, 'sanctum')
             ->getJson('/api/v1/reservations')
             ->assertStatus(200)
             ->assertJsonStructure(['data', 'meta']);
    }

    public function test_teacher_cannot_list_all_reservations(): void
    {
        $this->actingAs($this->teacher, 'sanctum')
             ->getJson('/api/v1/reservations')
             ->assertStatus(403);
    }

    public function test_admin_can_filter_reservations_by_status(): void
    {
        $this->actingAs($this->admin, 'sanctum')
             ->getJson('/api/v1/reservations?status=pending')
             ->assertStatus(200);
    }

    public function test_admin_can_filter_by_user(): void
    {
        $this->actingAs($this->admin, 'sanctum')
             ->getJson("/api/v1/reservations?user_id={$this->teacher->id}")
             ->assertStatus(200);
    }

    // ── SHOW ──────────────────────────────────────────────────

    public function test_admin_can_show_reservation(): void
    {
        $this->actingAs($this->admin, 'sanctum')
             ->getJson("/api/v1/reservations/{$this->reservation->id}")
             ->assertStatus(200)
             ->assertJsonStructure(['data' => ['id', 'status', 'user', 'start_date', 'end_date']]);
    }

    public function test_show_returns_404_for_unknown(): void
    {
        $this->actingAs($this->admin, 'sanctum')
             ->getJson('/api/v1/reservations/nonexistent-uuid')
             ->assertStatus(404);
    }

    // ── VALIDATE ──────────────────────────────────────────────

    public function test_admin_can_validate_reservation(): void
    {
        Event::fake();

        $this->actingAs($this->admin, 'sanctum')
             ->patchJson("/api/v1/reservations/{$this->reservation->id}/validate", [
                 'material_id' => $this->material->id,
             ])
             ->assertStatus(200)
             ->assertJsonPath('data.status', 'validated');

        $this->assertDatabaseHas('reservations', [
            'id'          => $this->reservation->id,
            'status'      => ReservationStatus::Validated->value,
            'material_id' => $this->material->id,
        ]);

        // Material should now be in_use
        $this->assertDatabaseHas('materials', [
            'id'     => $this->material->id,
            'status' => MaterialStatus::InUse->value,
        ]);
    }

    public function test_validate_fires_event(): void
    {
        Event::fake();

        $this->actingAs($this->admin, 'sanctum')
             ->patchJson("/api/v1/reservations/{$this->reservation->id}/validate", [
                 'material_id' => $this->material->id,
             ])
             ->assertStatus(200);

        Event::assertDispatched(\App\Events\ReservationValidated::class);
    }

    public function test_validate_fails_without_material_id(): void
    {
        $this->actingAs($this->admin, 'sanctum')
             ->patchJson("/api/v1/reservations/{$this->reservation->id}/validate", [])
             ->assertStatus(422)
             ->assertJsonStructure(['errors' => ['material_id']]);
    }

    public function test_validate_fails_with_unavailable_material(): void
    {
        $this->material->update(['status' => MaterialStatus::Broken->value]);

        $this->actingAs($this->admin, 'sanctum')
             ->patchJson("/api/v1/reservations/{$this->reservation->id}/validate", [
                 'material_id' => $this->material->id,
             ])
             ->assertStatus(422);
    }

    public function test_validate_fails_with_conflicting_reservation(): void
    {
        Event::fake();

        // Another validated reservation on same material same period
        Reservation::factory()->createOne([
            'user_id'     => $this->teacher->id,
            'material_id' => $this->material->id,
            'status'      => ReservationStatus::Validated->value,
            'start_date'  => now()->addDays(3)->format('Y-m-d'),
            'end_date'    => now()->addDays(6)->format('Y-m-d'),
        ]);

        $this->actingAs($this->admin, 'sanctum')
             ->patchJson("/api/v1/reservations/{$this->reservation->id}/validate", [
                 'material_id' => $this->material->id,
             ])
             ->assertStatus(422);
    }

    public function test_cannot_validate_already_validated_reservation(): void
    {
        Event::fake();

        $this->reservation->update(['status' => ReservationStatus::Validated->value]);

        $this->actingAs($this->admin, 'sanctum')
             ->patchJson("/api/v1/reservations/{$this->reservation->id}/validate", [
                 'material_id' => $this->material->id,
             ])
             ->assertStatus(422);
    }

    // ── REJECT ────────────────────────────────────────────────

    public function test_admin_can_reject_reservation(): void
    {
        Event::fake();

        $this->actingAs($this->admin, 'sanctum')
             ->patchJson("/api/v1/reservations/{$this->reservation->id}/reject", [
                 'reason' => 'Matériel indisponible pour cette période.',
             ])
             ->assertStatus(200)
             ->assertJsonPath('data.status', 'rejected')
             ->assertJsonPath('data.rejection_reason', 'Matériel indisponible pour cette période.');

        $this->assertDatabaseHas('reservations', [
            'id'     => $this->reservation->id,
            'status' => ReservationStatus::Rejected->value,
        ]);
    }

    public function test_reject_fires_event(): void
    {
        Event::fake();

        $this->actingAs($this->admin, 'sanctum')
             ->patchJson("/api/v1/reservations/{$this->reservation->id}/reject", [])
             ->assertStatus(200);

        Event::assertDispatched(\App\Events\ReservationRejected::class);
    }

    public function test_reject_works_without_reason(): void
    {
        Event::fake();

        $this->actingAs($this->admin, 'sanctum')
             ->patchJson("/api/v1/reservations/{$this->reservation->id}/reject", [])
             ->assertStatus(200)
             ->assertJsonPath('data.status', 'rejected');
    }

    public function test_reject_does_not_affect_material_status(): void
    {
        Event::fake();

        $originalStatus = $this->material->status->value;

        $this->actingAs($this->admin, 'sanctum')
             ->patchJson("/api/v1/reservations/{$this->reservation->id}/reject", [
                 'reason' => 'Test.',
             ])
             ->assertStatus(200);

        $this->assertDatabaseHas('materials', [
            'id'     => $this->material->id,
            'status' => $originalStatus,
        ]);
    }

    public function test_cannot_reject_already_rejected_reservation(): void
    {
        $this->reservation->update(['status' => ReservationStatus::Rejected->value]);

        $this->actingAs($this->admin, 'sanctum')
             ->patchJson("/api/v1/reservations/{$this->reservation->id}/reject", [])
             ->assertStatus(422);
    }

    public function test_teacher_cannot_validate_or_reject(): void
    {
        $this->actingAs($this->teacher, 'sanctum')
             ->patchJson("/api/v1/reservations/{$this->reservation->id}/validate", [
                 'material_id' => $this->material->id,
             ])
             ->assertStatus(403);

        $this->actingAs($this->teacher, 'sanctum')
             ->patchJson("/api/v1/reservations/{$this->reservation->id}/reject", [])
             ->assertStatus(403);
    }
}
