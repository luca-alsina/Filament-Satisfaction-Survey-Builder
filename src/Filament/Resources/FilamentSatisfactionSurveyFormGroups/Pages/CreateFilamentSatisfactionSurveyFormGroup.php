<?php

namespace Luca\FilamentSatisfactionSurveyBuilder\Filament\Resources\FilamentSatisfactionSurveyFormGroups\Pages;

use Filament\Resources\Pages\CreateRecord;
use Luca\FilamentSatisfactionSurveyBuilder\Filament\Resources\FilamentSatisfactionSurveyFormGroups\FilamentSatisfactionSurveyFormGroupResource;

class CreateFilamentSatisfactionSurveyFormGroup extends CreateRecord
{
    protected static string $resource = FilamentSatisfactionSurveyFormGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
