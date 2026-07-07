<?php

namespace Luca\FilamentSatisfactionSurveyBuilder\Listeners;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Luca\FilamentSatisfactionSurveyBuilder\Events\SurveyFormUserCreating;

class GenerateSurveyFormUserToken
{
    public function handle(SurveyFormUserCreating $event): void
    {
        if (empty($event->surveyFormUser->token)) {
            do {
                $token = Str::random(16);
            } while (DB::table('survey_form_users')->where('token', $token)->exists());

            $event->surveyFormUser->token = $token;
        }
    }
}
