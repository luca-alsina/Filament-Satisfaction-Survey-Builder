<?php

namespace Luca\FilamentSatisfactionSurveyBuilder\Filament\Resources\FilamentSatisfactionSurveyFormGroups\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Luca\FilamentSatisfactionSurveyBuilder\Filament\Resources\FilamentSatisfactionSurveyFormGroups\FilamentSatisfactionSurveyFormGroupResource;

class ListFilamentSatisfactionSurveyFormGroups extends ListRecords
{
    protected static string $resource = FilamentSatisfactionSurveyFormGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
