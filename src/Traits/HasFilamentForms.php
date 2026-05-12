<?php

namespace Luca\FilamentSatisfactionSurveyBuilder\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Luca\FilamentSatisfactionSurveyBuilder\Models\FilamentForm;

/** @phpstan-ignore-next-line */
trait HasFilamentForms
{
    public function FilamentForms(): BelongsToMany
    {
        return $this->belongsToMany(FilamentForm::class);
    }
}
