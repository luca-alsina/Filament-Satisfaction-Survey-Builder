<?php

namespace Luca\FilamentSatisfactionSurveyBuilder\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Luca\FilamentSatisfactionSurveyBuilder\Models\SurveyFormUser;

class SurveyFormUserCreating
{
    use Dispatchable;

    public function __construct(
        public SurveyFormUser $surveyFormUser
    ) {
    }
}
