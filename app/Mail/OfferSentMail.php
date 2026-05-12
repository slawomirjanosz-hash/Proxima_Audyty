<?php

namespace App\Mail;

use App\Models\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OfferSentMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Offer $offer,
    ) {}

    public function envelope(): Envelope
    {
        $label = ($this->offer->offer_number ? $this->offer->offer_number . ' – ' : '')
            . ($this->offer->offer_title ?: 'Oferta ENESA');

        return new Envelope(
            from: new Address('enesa.api@enesa.pl', 'ENESA System'),
            subject: 'Nowa oferta dla Twojej firmy – ' . $label,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.offer-sent',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
