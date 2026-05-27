<?php

namespace Luca\FilamentSatisfactionSurveyBuilder\Filament\Resources\FilamentSatisfactionSurveyFormResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Luca\FilamentSatisfactionSurveyBuilder\Filament\Resources\FilamentSatisfactionSurveyFormResource\FilamentSatisfactionSurveyFormResource;

class ListFilamentForms extends ListRecords
{
    protected static string $resource = FilamentSatisfactionSurveyFormResource::class;

    public function getTitle(): string
    {
        return config('filament-satisfaction-survey-builder.admin-panel-resource-name-plural');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Create ' . config('filament-satisfaction-survey-builder.admin-panel-resource-name')),
        ];
    }
}
