<?php

namespace App\Mail;

use App\Models\ClientRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrationReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly ClientRegistration $registration,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('enesa.api@enesa.pl', 'ENESA System'),
            subject: 'Dziękujemy za rejestrację – ' . $this->registration->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.registration-received',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
