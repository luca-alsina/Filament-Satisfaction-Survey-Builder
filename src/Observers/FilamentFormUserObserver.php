<?php

declare(strict_types=1);

namespace Luca\FilamentSatisfactionSurveyBuilder\Observers;

use Illuminate\Support\Facades\Mail;
use Luca\FilamentSatisfactionSurveyBuilder\Mail\FormSubmissionNotification;
use Luca\FilamentSatisfactionSurveyBuilder\Models\SurveyForm;
use Luca\FilamentSatisfactionSurveyBuilder\Models\SurveyFormUser;

class FilamentFormUserObserver
{
    public function created(SurveyFormUser $filamentFormUser): void
    {
        $this->sendNotifications($filamentFormUser);
    }

    public function updated(SurveyFormUser $filamentFormUser): void
    {
        // Log that the updated event fired
        \Log::info('FilamentFormUserObserver::updated() fired for entry ID: '.$filamentFormUser->id);

        $this->sendNotifications($filamentFormUser);
    }

    protected function sendNotifications(SurveyFormUser $filamentFormUser): void
    {
        /** @var SurveyForm|null $form */
        $form = $filamentFormUser->filamentForm;

        // Check if notification emails are configured for this form
        if (! $form || ! $form->notification_emails) {
            \Log::info('No notification emails configured for form ID: '.($form->id ?? 'null'));

            return;
        }

        // Filter out empty email addresses and send notifications
        $emails = array_filter($form->notification_emails);

        if (empty($emails)) {
            \Log::info('Notification emails array is empty after filtering');

            return;
        }

        \Log::info('Sending notifications for entry ID: '.$filamentFormUser->id.' to: '.json_encode($emails));

        foreach ($emails as $email) {
            Mail::to($email)->queue(new FormSubmissionNotification($form, $filamentFormUser));
        }

        \Log::info('Queued '.count($emails).' notification emails for entry ID: '.$filamentFormUser->id);
    }
}
