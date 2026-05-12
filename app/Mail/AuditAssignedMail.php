<?php

namespace App\Mail;

use App\Models\Company;
use App\Models\EnergyAudit;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AuditAssignedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly EnergyAudit $audit,
        public readonly Company     $company,
        public readonly User        $recipient,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('enesa.api@enesa.pl', 'ENESA System'),
            subject: 'Audyt energetyczny przydzielony – ' . $this->company->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.audit-assigned',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
