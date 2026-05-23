<?php


namespace App\Providers;

use App\Events\ReservationRejected;
use App\Events\ReservationSubmitted;
use App\Events\ReservationValidated;
use App\Listeners\SendAdminNotification;
use App\Listeners\SendReservationRejectedNotification;
use App\Listeners\SendReservationSubmittedNotification;
use App\Listeners\SendReservationValidatedNotification;
use App\Services\TwilioService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
{
    $this->app->singleton(TwilioService::class, function () {
        return new TwilioService(
            sid:   config('services.twilio.sid',   ''),
            token: config('services.twilio.token', ''),
            from:  config('services.twilio.from',  ''),
        );
    });
}
    public function boot(): void
    {

        Event::listen(ReservationSubmitted::class, SendReservationSubmittedNotification::class);


        Event::listen(ReservationSubmitted::class, SendAdminNotification::class);


        Event::listen(ReservationValidated::class, SendReservationValidatedNotification::class);
        Event::listen(ReservationRejected::class,  SendReservationRejectedNotification::class);
    }
}
