<?php

namespace Luca\FilamentSatisfactionSurveyBuilder\Filament\Resources\FilamentSatisfactionSurveyFormGroups\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Luca\FilamentSatisfactionSurveyBuilder\Filament\Resources\FilamentSatisfactionSurveyFormGroups\FilamentSatisfactionSurveyFormGroupResource;

class EditFilamentSatisfactionSurveyFormGroup extends EditRecord
{
    protected static string $resource = FilamentSatisfactionSurveyFormGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
