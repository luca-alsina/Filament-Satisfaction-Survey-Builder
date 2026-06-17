<?php

namespace Luca\FilamentSatisfactionSurveyBuilder\Filament\Resources\FilamentSatisfactionSurveyFormResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Colors\Color;
use Luca\FilamentSatisfactionSurveyBuilder\Filament\Resources\FilamentSatisfactionSurveyFormResource\FilamentSatisfactionSurveyFormResource;
use Luca\FilamentSatisfactionSurveyBuilder\Jobs\CalculateAverageDataJob;
use Luca\FilamentSatisfactionSurveyBuilder\Models\SurveyForm;

class EditFilamentForm extends EditRecord
{
    protected static string $resource = FilamentSatisfactionSurveyFormResource::class;

    public function getTitle(): string
    {
        return 'Edit ' . config('filament-satisfaction-survey-builder.admin-panel-resource-name');
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            Action::make('preview')
                ->visible(fn() => (bool)config('filament-satisfaction-survey-builder.preview-route'))
                ->url(fn($record) => route(config('filament-satisfaction-survey-builder.preview-route'), ['form' => $record->id]))
                ->openUrlInNewTab(),
            Action::make('regenerate_average')
                ->color(Color::Amber)
                ->action(function (SurveyForm $record) {
                    $record->update([
                        'average_data' => null
                    ]);
                    CalculateAverageDataJob::dispatch($record);
                })
                ->successNotification(fn() => Notification::make()->title(__('regenerate_average_in_progress')))
        ];
    }
}
