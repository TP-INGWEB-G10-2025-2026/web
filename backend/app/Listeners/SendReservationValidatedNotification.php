<?php


namespace App\Listeners;

use App\Events\ReservationValidated;
use App\Mail\ReservationValidatedMail;
use App\Services\TwilioService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendReservationValidatedNotification
{
    public function __construct(private readonly TwilioService $twilioService) {}

    public function handle(ReservationValidated $event): void
    {
        $reservation = $event->reservation->load(['user', 'material.category']);
        $user        = $reservation->user;

        try {
            Mail::to($user->email)->queue(new ReservationValidatedMail($reservation));
        } catch (\Exception $e) {
            Log::error('ReservationValidated email error: ' . $e->getMessage());
        }

        if ($user->phone) {
            $this->twilioService->sendSms(
                to     : $user->phone,
                message: " Votre réservation du {$reservation->start_date->format('d/m/Y')} au {$reservation->end_date->format('d/m/Y')} a été validée. Matériel : {$reservation->material->name}.",
            );
        }
    }
}
