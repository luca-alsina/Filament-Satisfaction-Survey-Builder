<?php

namespace Luca\FilamentSatisfactionSurveyBuilder\Filament\Resources\FilamentFormResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Luca\FilamentSatisfactionSurveyBuilder\Filament\Resources\FilamentFormResource;

class ListFilamentForms extends ListRecords
{
    protected static string $resource = FilamentFormResource::class;

    public function getTitle(): string
    {
        return config('filament-satisfaction-survey-builder.admin-panel-resource-name-plural');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Create '.config('filament-satisfaction-survey-builder.admin-panel-resource-name')),
        ];
    }
}
