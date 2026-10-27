<?php

namespace Luca\FilamentSatisfactionSurveyBuilder\Contracts;

use Luca\FilamentSatisfactionSurveyBuilder\Models\SurveyFormUser;

interface FormSubmissionRedirectResolver
{
    public function resolve(string $redirectUrl, SurveyFormUser $entry): string;
}
