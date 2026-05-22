<?php


namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Reservation $reservation) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: '❌ Réservation rejetée');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.reservations.rejected');
    }
}
