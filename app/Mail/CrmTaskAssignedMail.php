<?php

namespace App\Mail;

use App\Models\CrmTask;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CrmTaskAssignedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly CrmTask $task,
        public readonly User    $assignee,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('enesa.api@enesa.pl', 'ENESA System'),
            subject: 'Nowe zadanie CRM: ' . $this->task->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.crm-task-assigned',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
