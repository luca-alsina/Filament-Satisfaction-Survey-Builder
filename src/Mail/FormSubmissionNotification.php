<?php

declare(strict_types=1);

namespace Luca\FilamentSatisfactionSurveyBuilder\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Luca\FilamentSatisfactionSurveyBuilder\Models\FilamentForm;
use Luca\FilamentSatisfactionSurveyBuilder\Models\FilamentFormUser;

class FormSubmissionNotification extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public FilamentForm $form,
        public FilamentFormUser $entry,
    ) {}

    public function envelope(): Envelope
    {
        $appName = config('app.name');

        return new Envelope(
            subject: "New {$appName} Form Submission: ".$this->form->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'filament-satisfaction-survey-builder::mail.submission-notification',
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
