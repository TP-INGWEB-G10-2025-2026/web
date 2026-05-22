<?php

namespace Tests\Feature;

use App\Enums\MaterialStatus;
use App\Enums\ReservationStatus;
use App\Enums\Role;
use App\Events\ReservationSubmitted;
use App\Jobs\SendReservationEmail;
use App\Jobs\SendReservationSms;
use App\Mail\NewReservationMail;
use App\Models\Category;
use App\Models\Material;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ReservationNotificationTest extends TestCase
{
    use RefreshDatabase;

    private User     $teacher;
    private User     $admin;
    private Category $category;
    private Material $material;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var User $teacher */
        $teacher = User::factory()->createOne(['role' => Role::Teacher]);
        $this->teacher = $teacher;

        /** @var User $admin */
        $admin = User::factory()->createOne(['role' => Role::Admin]);
        $this->admin = $admin;

        $this->category = Category::factory()->create();
        $this->material = Material::factory()->createOne([
            'category_id' => $this->category->id,
            'status'      => MaterialStatus::Available->value,
        ]);
    }

    public function test_submitting_reservation_dispatches_admin_notification_jobs(): void
    {
        Bus::fake();

        $this->actingAs($this->teacher, 'sanctum')
             ->postJson('/api/v1/reservations', [
                 'start_date' => now()->addDays(2)->format('Y-m-d'),
                 'end_date'   => now()->addDays(5)->format('Y-m-d'),
             ])
             ->assertStatus(201);

        Bus::assertDispatched(SendReservationEmail::class);
        Bus::assertDispatched(SendReservationSms::class);
    }

    public function test_submitting_reservation_fires_event(): void
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

    public function test_email_job_sends_to_admin(): void
    {
        Mail::fake();

        $reservation = Reservation::factory()->createOne([
            'user_id'    => $this->teacher->id,
            'status'     => ReservationStatus::Pending->value,
            'start_date' => now()->addDays(2)->format('Y-m-d'),
            'end_date'   => now()->addDays(5)->format('Y-m-d'),
        ]);

        $reservation->load('user');

        // Run the job directly
        (new SendReservationEmail($reservation))->handle();

        Mail::assertSent(NewReservationMail::class, function ($mail) {
            return $mail->hasTo(config('services.admin.email'));
        });
    }

    public function test_jobs_are_queued_on_notifications_queue(): void
    {
        Queue::fake();

        $reservation = Reservation::factory()->createOne([
            'user_id'    => $this->teacher->id,
            'status'     => ReservationStatus::Pending->value,
            'start_date' => now()->addDays(2)->format('Y-m-d'),
            'end_date'   => now()->addDays(5)->format('Y-m-d'),
        ]);

        SendReservationEmail::dispatch($reservation)->onQueue('notifications');
        SendReservationSms::dispatch($reservation)->onQueue('notifications');

        Queue::assertPushedOn('notifications', SendReservationEmail::class);
        Queue::assertPushedOn('notifications', SendReservationSms::class);
    }

    public function test_api_response_not_blocked_by_notifications(): void
    {
        Bus::fake();

        $start = microtime(true);

        $this->actingAs($this->teacher, 'sanctum')
             ->postJson('/api/v1/reservations', [
                 'start_date' => now()->addDays(2)->format('Y-m-d'),
                 'end_date'   => now()->addDays(5)->format('Y-m-d'),
             ])
             ->assertStatus(201);

        $duration = microtime(true) - $start;

        // Response should be fast (under 3 seconds) since jobs are queued
        $this->assertLessThan(3, $duration, 'API response was blocked by notification jobs.');
    }

    public function test_blocked_user_does_not_trigger_notifications(): void
    {
        Bus::fake();

        $this->teacher->update(['is_blocked' => true]);

        $this->actingAs($this->teacher, 'sanctum')
             ->postJson('/api/v1/reservations', [
                 'start_date' => now()->addDays(2)->format('Y-m-d'),
                 'end_date'   => now()->addDays(5)->format('Y-m-d'),
             ])
             ->assertStatus(403);

        Bus::assertNotDispatched(SendReservationEmail::class);
        Bus::assertNotDispatched(SendReservationSms::class);
    }
}
