<?php

namespace App\Jobs;

use App\Mail\NewReservationMail;
use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendReservationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 30;

    public function __construct(public readonly Reservation $reservation) {}

    public function handle(): void
    {
        $adminEmail = config('services.admin.email');

        if (! $adminEmail) {
            Log::warning('SendReservationEmail: ADMIN_EMAIL not configured.');
            return;
        }

        try {
            Mail::to($adminEmail)->send(new NewReservationMail($this->reservation));
            Log::info("SendReservationEmail: sent to {$adminEmail} for reservation {$this->reservation->id}");
        } catch (\Exception $e) {
            Log::error('SendReservationEmail failed: ' . $e->getMessage());
            throw $e; // re-throw so queue retries
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::error("SendReservationEmail permanently failed for reservation {$this->reservation->id}: " . $e->getMessage());
    }
}
