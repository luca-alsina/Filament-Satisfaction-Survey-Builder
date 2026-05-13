<?php

namespace Luca\FilamentSatisfactionSurveyBuilder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SurveyFormGroup extends Model
{

    public function filamentFormGroupFields(): HasMany
    {
        return $this->hasMany(SurveyFormGroupField::class)
            ->orderBy('order', 'asc');
    }

    public function filamentForm(): BelongsTo
    {
        return $this->belongsTo(SurveyForm::class);
    }
}
