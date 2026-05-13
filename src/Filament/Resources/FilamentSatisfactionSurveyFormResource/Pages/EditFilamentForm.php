<?php

namespace Luca\FilamentSatisfactionSurveyBuilder\Filament\Resources\FilamentSatisfactionSurveyFormResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Luca\FilamentSatisfactionSurveyBuilder\Filament\Resources\FilamentSatisfactionSurveyFormResource;

class EditFilamentForm extends EditRecord
{
    protected static string $resource = FilamentSatisfactionSurveyFormResource::class;

    public function getTitle(): string
    {
        return 'Edit '.config('filament-satisfaction-survey-builder.admin-panel-resource-name');
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            Action::make('preview')
                ->visible(fn () => (bool) config('filament-satisfaction-survey-builder.preview-route'))
                ->url(fn ($record) => route(config('filament-satisfaction-survey-builder.preview-route'), ['form' => $record->id]))
                ->openUrlInNewTab(),
        ];
    }
}
