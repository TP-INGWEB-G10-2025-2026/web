<?php


namespace App\Listeners;

use App\Events\ReservationSubmitted;
use App\Jobs\SendReservationEmail;
use App\Jobs\SendReservationSms;
use Illuminate\Support\Facades\Log;

class SendAdminNotification
{
    public function handle(ReservationSubmitted $event): void
    {
        $reservation = $event->reservation->load('user');

        Log::info("SendAdminNotification: dispatching jobs for reservation {$reservation->id}");

      
        SendReservationEmail::dispatch($reservation)->onQueue('notifications');
        SendReservationSms::dispatch($reservation)->onQueue('notifications');
    }
}
