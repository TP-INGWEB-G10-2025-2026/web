<?php


namespace App\Mail;

use App\Models\Loan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DamagedMaterialMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Loan $loan) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⚠️ Matériel ' . ($this->loan->return_status->value === 'lost' ? 'perdu' : 'endommagé')
                   . ' — ' . $this->loan->material->name,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.loans.damaged-material');
    }
}
