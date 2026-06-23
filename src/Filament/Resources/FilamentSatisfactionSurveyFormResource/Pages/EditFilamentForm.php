<?php

namespace Luca\FilamentSatisfactionSurveyBuilder\Filament\Resources\FilamentSatisfactionSurveyFormResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
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
            ActionGroup::make([
                Action::make('download_average_pdf')
                    ->label(__('filament-satisfaction-survey-builder::filament-resources.survey-form.actions.download_average_pdf'))
                    ->icon('heroicon-o-chart-bar')
                    ->visible(fn(SurveyForm $record): bool => !empty($this->getAverageDataRows($record)))
                    ->openUrlInNewTab()
                    ->url(fn(SurveyForm $record) => route('filament-satisfaction-survey-builder.pdf.average', ['form' => $record])),
                Action::make('download_responses_pdf')
                    ->label(__('filament-satisfaction-survey-builder::filament-resources.survey-form.actions.download_responses_pdf'))
                    ->icon('heroicon-o-users')
                    ->visible(fn(SurveyForm $record): bool => $record->filamentFormUsers()->exists())
                    ->openUrlInNewTab()
                    ->url(fn(SurveyForm $record) => route('filament-satisfaction-survey-builder.pdf.responses', ['form' => $record])),
            ])
                ->label(__('filament-satisfaction-survey-builder::filament-resources.survey-form.actions.documents_group'))
                ->icon('heroicon-o-document-arrow-down')
                ->button(),
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

    /**
     * @return array<int, array{
     *     field_id: int|string,
     *     label: string,
     *     field_type: string|null,
     *     average_type: int|null,
     *     average: mixed
     * }>
     */
    protected function getAverageDataRows(SurveyForm $record): array
    {
        if (!is_array($record->average_data) || empty($record->average_data)) {
            return [];
        }

        return collect($record->average_data)
            ->map(function ($fieldData, $fieldId): array {
                $fieldData = is_array($fieldData) ? $fieldData : [];

                return [
                    'field_id' => $fieldId,
                    'label' => (string)($fieldData['label'] ?? ('#' . $fieldId)),
                    'field_type' => $fieldData['field_type'] ?? null,
                    'average_type' => isset($fieldData['average_type']) ? (int)$fieldData['average_type'] : null,
                    'average' => $fieldData['average'] ?? null,
                ];
            })
            ->values()
            ->all();
    }
}
