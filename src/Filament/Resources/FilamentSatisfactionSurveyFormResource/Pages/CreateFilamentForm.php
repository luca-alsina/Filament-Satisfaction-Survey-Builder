<?php

namespace Luca\FilamentSatisfactionSurveyBuilder\Filament\Resources\FilamentSatisfactionSurveyFormResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Luca\FilamentSatisfactionSurveyBuilder\Filament\Resources\FilamentSatisfactionSurveyFormResource\FilamentSatisfactionSurveyFormResource;

class CreateFilamentForm extends CreateRecord
{
    protected static string $resource = FilamentSatisfactionSurveyFormResource::class;

    public function getTitle(): string
    {
        return 'Create ' . config('filament-satisfaction-survey-builder.admin-panel-resource-name');
    }
}
