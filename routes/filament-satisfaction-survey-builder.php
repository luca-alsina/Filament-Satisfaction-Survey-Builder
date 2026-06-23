<?php

use Illuminate\Support\Str;
use Luca\FilamentSatisfactionSurveyBuilder\Enums\FilamentFieldTypeEnum;
use Luca\FilamentSatisfactionSurveyBuilder\Models\SurveyForm;
use Spatie\Browsershot\Browsershot;
use Spatie\LaravelPdf\Facades\Pdf;

Route::get('/survey-forms/{form}/average-pdf', function (SurveyForm $form) {
    $averageDataRows = $form->average_data; // Assurez-vous d'avoir accès à ces données

    if (empty($averageDataRows)) {
        abort(404, 'Aucune donnée moyenne disponible.');
    }

    $filename = Str::slug($form->name) . '-average-stats-' . now()->format('Y-m-d-His') . '.pdf';

    return Pdf::view('filament-satisfaction-survey-builder::pdf.average-data', [
        'form' => $form,
        'averageDataRows' => collect($averageDataRows)->map(function ($fieldData, $fieldId) {
            return [
                'field_id'     => $fieldId,
                'label'        => (string)($fieldData['label'] ?? ('#' . $fieldId)),
                'field_type'   => isset($fieldData['field_type'])
                    ? (FilamentFieldTypeEnum::fromString($fieldData['field_type'])?->getLabel() ?? $fieldData['field_type'])
                    : null,
                'average_type' => isset($fieldData['average_type']) ? (int)$fieldData['average_type'] : null,
                'average'      => $fieldData['average'] ?? null,
            ];
        })->values()->all(),
        'generatedAt' => now(),
    ])
        ->format('a4')
        ->withBrowsershot(fn(Browsershot $browsershot) => $browsershot->noSandbox())
        ->name($filename)
        ->inline();
})->middleware('web')->name('filament-satisfaction-survey-builder.pdf.average');

Route::get('/survey-forms/{form}/responses-pdf', function (SurveyForm $form) {
    $entries = $form->filamentFormUsers()->with('user')->get();

    if ($entries->isEmpty()) {
        abort(404, 'Aucune réponse disponible.');
    }

    // Build a flat map of field id => label from average_data (fallback to raw id)
    $fields = [];
    if (is_array($form->average_data)) {
        foreach ($form->average_data as $fieldId => $fieldData) {
            $fields[$fieldId] = (string) ($fieldData['label'] ?? ('#' . $fieldId));
        }
    }

    $filename = Str::slug($form->name) . '-responses-' . now()->format('Y-m-d-His') . '.pdf';

    return Pdf::view('filament-satisfaction-survey-builder::pdf.responses-data', [
        'form'        => $form,
        'entries'     => $entries,
        'fields'      => $fields,
        'generatedAt' => now(),
    ])
        ->format('a4')
        ->withBrowsershot(fn(Browsershot $browsershot) => $browsershot->noSandbox())
        ->name($filename)
        ->inline();
})->middleware('web')->name('filament-satisfaction-survey-builder.pdf.responses');
