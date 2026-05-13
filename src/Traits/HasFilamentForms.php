<?php

namespace Luca\FilamentSatisfactionSurveyBuilder\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Luca\FilamentSatisfactionSurveyBuilder\Models\SurveyForm;

/** @phpstan-ignore-next-line */
trait HasFilamentForms
{
    public function FilamentForms(): BelongsToMany
    {
        return $this->belongsToMany(SurveyForm::class);
    }
}
