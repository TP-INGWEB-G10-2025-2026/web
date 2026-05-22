<?php

namespace App\Listeners;

use App\Events\ReservationSubmitted;
use App\Mail\ReservationSubmittedMail;
use App\Services\TwilioService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendReservationSubmittedNotification
{
    public function __construct(private readonly TwilioService $twilioService) {}

    public function handle(ReservationSubmitted $event): void
    {
        $reservation = $event->reservation->load(['user', 'material']);
        $user        = $reservation->user;

        // Email via SendGrid
        try {
            Mail::to($user->email)->queue(new ReservationSubmittedMail($reservation));
        } catch (\Exception $e) {
            Log::error('ReservationSubmitted email error: ' . $e->getMessage());
        }

        // SMS via Twilio
        if ($user->phone) {
            try {
                $this->twilioService->sendSms(
                    to: $user->phone,
                    message: "Votre demande de réservation du {$reservation->start_date->format('d/m/Y')} au {$reservation->end_date->format('d/m/Y')} a été soumise. Statut : En attente.",
                );
            } catch (\Exception $e) {
                Log::error('ReservationSubmitted SMS error: ' . $e->getMessage());
            }
        }
    }
}
