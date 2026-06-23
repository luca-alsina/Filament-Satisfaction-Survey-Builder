<?php

use Illuminate\Support\Str;
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
                'field_id' => $fieldId,
                'label' => (string)($fieldData['label'] ?? ('#' . $fieldId)),
                'field_type' => $fieldData['field_type'] ?? null,
                'average_type' => isset($fieldData['average_type']) ? (int)$fieldData['average_type'] : null,
                'average' => $fieldData['average'] ?? null,
            ];
        })->values()->all(),
        'generatedAt' => now(),
    ])
        ->format('a4')
        ->withBrowsershot(fn(Browsershot $browsershot) => $browsershot->noSandbox())
        ->name($filename)
        ->inline();
})->middleware('web')->name('filament-satisfaction-survey-builder.pdf.average');
