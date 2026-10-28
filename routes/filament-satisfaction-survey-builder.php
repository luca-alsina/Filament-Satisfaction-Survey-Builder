<?php

use Illuminate\Support\Str;
use Luca\FilamentSatisfactionSurveyBuilder\Enums\FilamentFieldTypeEnum;
use Luca\FilamentSatisfactionSurveyBuilder\Models\SurveyForm;
use Spatie\Browsershot\Browsershot;
use Spatie\LaravelPdf\Facades\Pdf;

Route::get('/survey-forms/{form}/average-pdf', function ($form) {
    $formModel = SurveyForm::configuredClass();
    $form = $form instanceof SurveyForm ? $form : $formModel::query()->whereKey($form)->firstOrFail();
    $averageDataRows = $form->average_data; // Assurez-vous d'avoir accès à ces données

    if (empty($averageDataRows)) {
        abort(404, 'Aucune donnée moyenne disponible.');
    }

    $filename = Str::slug($form->name) . '-average-stats-' . now()->format('Y-m-d-His') . '.pdf';

    // Taux de réponse : nombre de réponses par rapport au nombre de personnes inscrites
    $totalRegistered = $form->filamentFormUsers()->count();
    $totalResponses = $form->filamentFormUsers()->whereNotNull('entry')->count();
    $responseRate = $totalRegistered > 0 ? ($totalResponses / $totalRegistered) * 100 : null;

    return Pdf::view('filament-satisfaction-survey-builder::pdf.average-data', [
        'form' => $form,
        'totalRegistered' => $totalRegistered,
        'totalResponses' => $totalResponses,
        'responseRate' => $responseRate,
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

Route::get('/survey-forms/{form}/synthesis-pdf', function ($form) {
    $formModel = SurveyForm::configuredClass();
    $form = $form instanceof SurveyForm ? $form : $formModel::query()->whereKey($form)->firstOrFail();
    $form->load('filamentFormGroups', 'filamentFormGroups.filamentFormGroupFields');
    $entries = $form->filamentFormUsers()->with('user')->get();

    $averageDataRows = collect($form->average_data ?? [])->map(function ($fieldData, $fieldId) {
        return [
            'field_id' => (string) $fieldId,
            'label' => (string) ($fieldData['label'] ?? ('#' . $fieldId)),
            'field_type' => isset($fieldData['field_type'])
                ? (FilamentFieldTypeEnum::fromString($fieldData['field_type'])?->getLabel() ?? $fieldData['field_type'])
                : null,
            'average_type' => isset($fieldData['average_type']) ? (int) $fieldData['average_type'] : null,
            'average' => $fieldData['average'] ?? null,
        ];
    })->keyBy('field_id');

    $textFieldTypes = [
        FilamentFieldTypeEnum::TEXT,
        FilamentFieldTypeEnum::TEXTAREA,
        FilamentFieldTypeEnum::RICH_EDITOR,
        FilamentFieldTypeEnum::MARKDOWN_EDITOR,
    ];
    $textFieldsById = [];
    $textFieldIdsByLabel = [];
    $questionGroups = [];

    foreach ($form->filamentFormGroups as $group) {
        $groupData = [
            'id' => $group->id,
            'name' => $group->name,
            'description' => $group->description,
            'averageDataRows' => [],
            'textFields' => [],
        ];

        foreach ($group->filamentFormGroupFields as $field) {
            $fieldId = (string) $field->id;
            if ($averageDataRows->has($fieldId)) {
                $groupData['averageDataRows'][] = $averageDataRows->pull($fieldId);
            }

            if (in_array($field->type, $textFieldTypes, true)) {
                $textField = ['id' => $fieldId, 'label' => $field->label];
                $groupData['textFields'][] = $textField;
                $textFieldsById[$fieldId] = $textField;
                $textFieldIdsByLabel[$field->label] ??= $fieldId;
            }
        }

        if ($groupData['averageDataRows'] || $groupData['textFields']) {
            $questionGroups[] = $groupData;
        }
    }

    if ($averageDataRows->isNotEmpty()) {
        $questionGroups[] = [
            'id' => null,
            'name' => __('filament-satisfaction-survey-builder::filament-resources.survey-form.pdf.synthesis.ungrouped'),
            'description' => null,
            'averageDataRows' => $averageDataRows->values()->all(),
            'textFields' => [],
        ];
    }

    $textResponseGroups = array_values(array_filter(
        $questionGroups,
        fn (array $group): bool => ! empty($group['textFields']),
    ));

    $respondents = $entries->map(function ($entry) use ($textFieldsById, $textFieldIdsByLabel) {
        $answers = [];
        foreach (is_array($entry->entry) ? $entry->entry : [] as $fieldEntry) {
            if (! is_array($fieldEntry)) {
                continue;
            }

            $fieldId = (string) ($fieldEntry['field_id'] ?? $fieldEntry['field'] ?? '');
            if (! isset($textFieldsById[$fieldId])) {
                $fieldId = $textFieldIdsByLabel[(string) ($fieldEntry['field'] ?? '')] ?? '';
            }
            if (! isset($textFieldsById[$fieldId])) {
                continue;
            }

            $answer = $fieldEntry['answer'] ?? null;
            if (is_array($answer)) {
                $answer = implode(', ', array_filter(array_map('strval', $answer)));
            } elseif (is_string($answer)) {
                // Rich editor answers are stored as HTML; render their text in the PDF.
                $answer = preg_replace('/<\s*(br\s*\/?>|\/(p|div)\s*)>/i', "\n", $answer);
                $answer = html_entity_decode(strip_tags($answer), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }

            $answers[$fieldId] = $answer;
        }

        return [
            'name' => $entry->user?->name ?? __('filament-satisfaction-survey-builder::filament-resources.survey-form.pdf.responses.guest'),
            'email' => $entry->user?->email,
            'submitted_at' => $entry->created_at?->format('Y-m-d H:i') ?? '—',
            'answers' => $answers,
        ];
    });

    $totalRegistered = $form->filamentFormUsers()->count();
    $totalResponses = $form->filamentFormUsers()->whereNotNull('entry')->count();
    $responseRate = $totalRegistered > 0 ? ($totalResponses / $totalRegistered) * 100 : null;
    $filename = Str::slug($form->name) . '-synthesis-' . now()->format('Y-m-d-His') . '.pdf';

    return Pdf::view('filament-satisfaction-survey-builder::pdf.average-data', [
        'form' => $form,
        'totalRegistered' => $totalRegistered,
        'totalResponses' => $totalResponses,
        'responseRate' => $responseRate,
        'averageDataRows' => $averageDataRows->values()->all(),
        'questionGroups' => $questionGroups,
        'textResponseGroups' => $textResponseGroups,
        'respondents' => $respondents,
        'generatedAt' => now(),
        'documentTitle' => __('filament-satisfaction-survey-builder::filament-resources.survey-form.actions.download_synthesis_pdf'),
    ])
        ->format('a4')
        ->withBrowsershot(fn (Browsershot $browsershot) => $browsershot->noSandbox())
        ->name($filename)
        ->inline();
})->middleware('web')->name('filament-satisfaction-survey-builder.pdf.synthesis');

Route::get('/survey-forms/{form}/responses-pdf', function ($form) {
    $formModel = SurveyForm::configuredClass();
    $form = $form instanceof SurveyForm ? $form : $formModel::query()->whereKey($form)->firstOrFail();
    $form->load('filamentFormGroups', 'filamentFormGroups.filamentFormGroupFields');
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

    // Map field id => groupe (comme les Sections dans Show.php)
    $fieldGroups = [];
    $formGroups = [];
    foreach ($form->filamentFormGroups as $group) {
        $formGroups[] = [
            'id' => $group->id,
            'name' => $group->name,
            'description' => $group->description,
        ];
        foreach ($group->filamentFormGroupFields as $groupField) {
            $fieldGroups[(string) $groupField->id] = [
                'id' => $group->id,
                'name' => $group->name,
                'description' => $group->description,
            ];
            if (! isset($fields[(string) $groupField->id]) && ! isset($fields[$groupField->id])) {
                $fields[(string) $groupField->id] = (string) $groupField->label;
            }
        }
    }

    $filename = Str::slug($form->name) . '-responses-' . now()->format('Y-m-d-His') . '.pdf';

    // Statistiques globales : mêmes informations que le PDF des moyennes
    $totalRegistered = $form->filamentFormUsers()->count();
    $totalResponses = $form->filamentFormUsers()->whereNotNull('entry')->count();
    $responseRate = $totalRegistered > 0 ? ($totalResponses / $totalRegistered) * 100 : null;

    // Statistiques par question depuis average_data (même mapping que average-pdf)
    $averageDataRows = collect($form->average_data ?? [])->map(function ($fieldData, $fieldId) {
        return [
            'field_id'     => $fieldId,
            'label'        => (string)($fieldData['label'] ?? ('#' . $fieldId)),
            'field_type'   => isset($fieldData['field_type'])
                ? (FilamentFieldTypeEnum::fromString($fieldData['field_type'])?->getLabel() ?? $fieldData['field_type'])
                : null,
            'average_type' => isset($fieldData['average_type']) ? (int)$fieldData['average_type'] : null,
            'average'      => $fieldData['average'] ?? null,
        ];
    })->values()->all();

    return Pdf::view('filament-satisfaction-survey-builder::pdf.responses-data', [
        'form'        => $form,
        'entries'     => $entries,
        'fields'      => $fields,
        'fieldGroups' => $fieldGroups,
        'formGroups'  => $formGroups,
        'totalRegistered' => $totalRegistered,
        'totalResponses' => $totalResponses,
        'responseRate' => $responseRate,
        'averageDataRows' => $averageDataRows,
        'generatedAt' => now(),
    ])
        ->format('a4')
        ->withBrowsershot(fn(Browsershot $browsershot) => $browsershot->noSandbox())
        ->name($filename)
        ->inline();
})->middleware('web')->name('filament-satisfaction-survey-builder.pdf.responses');
