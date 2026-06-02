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
//        return config('filament-satisfaction-survey-builder.admin-panel-resource-name-plural');
        return __('filament-satisfaction-survey-builder::filament-resources.survey-form.name.plural');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('Create') . ' ' . __('filament-satisfaction-survey-builder::filament-resources.survey-form.name.singular')),
        ];
    }
}
