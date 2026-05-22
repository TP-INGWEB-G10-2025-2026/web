<?php


namespace App\Jobs;

use App\Models\Reservation;
use App\Services\TwilioService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendReservationSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 30;

    public function __construct(public readonly Reservation $reservation) {}

    public function handle(TwilioService $twilio): void
    {
        $adminPhone = config('services.admin.phone');

        if (! $adminPhone) {
            Log::warning('SendReservationSms: ADMIN_PHONE not configured.');
            return;
        }

        $reservation = $this->reservation->load('user');
        $teacher     = $reservation->user;
        $start       = $reservation->start_date->format('d/m/Y');
        $end         = $reservation->end_date->format('d/m/Y');

        $message = " Nouvelle demande de réservation\n"
                 . "Enseignant : {$teacher->name}\n"
                 . "Période : du {$start} au {$end}\n"
                 . "Statut : En attente de validation.";

        try {
            $twilio->sendSms(to: $adminPhone, message: $message);
            Log::info("SendReservationSms: sent to {$adminPhone} for reservation {$this->reservation->id}");
        } catch (\Exception $e) {
            Log::error('SendReservationSms failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::error("SendReservationSms permanently failed for reservation {$this->reservation->id}: " . $e->getMessage());
    }
}
