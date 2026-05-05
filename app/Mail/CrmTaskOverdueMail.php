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

class CrmTaskOverdueMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  \Illuminate\Support\Collection<int, CrmTask>  $tasks
     */
    public function __construct(
        public readonly User                         $assignee,
        public readonly \Illuminate\Support\Collection $tasks,
    ) {}

    public function envelope(): Envelope
    {
        $count = $this->tasks->count();
        $label = $count === 1 ? '1 zadanie' : "{$count} zadania";

        return new Envelope(
            from: new Address('enesa.api@enesa.pl', 'ENESA System'),
            subject: "⚠ CRM: {$label} " . ($count === 1 ? 'jest po terminie' : 'są po terminie'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.crm-task-overdue',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
