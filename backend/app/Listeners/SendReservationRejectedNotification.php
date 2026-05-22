<?php


namespace App\Listeners;

use App\Events\ReservationRejected;
use App\Mail\ReservationRejectedMail;
use App\Services\TwilioService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendReservationRejectedNotification
{
    public function __construct(private readonly TwilioService $twilioService) {}

    public function handle(ReservationRejected $event): void
    {
        $reservation = $event->reservation->load(['user', 'material']);
        $user        = $reservation->user;

        try {
            Mail::to($user->email)->queue(new ReservationRejectedMail($reservation));
        } catch (\Exception $e) {
            Log::error('ReservationRejected email error: ' . $e->getMessage());
        }

        if ($user->phone) {
            $reason = $reservation->rejection_reason ? " Raison : {$reservation->rejection_reason}" : '';
            $this->twilioService->sendSms(
                to     : $user->phone,
                message: " Votre réservation du {$reservation->start_date->format('d/m/Y')} au {$reservation->end_date->format('d/m/Y')} a été rejetée.{$reason}",
            );
        }
    }
}
