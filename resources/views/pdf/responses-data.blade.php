<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $form->name }} - Réponses individuelles</title>
    <style>
        @page {
            margin: 18mm 12mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #111827;
            margin: 0;
        }

        h1 {
            font-size: 20px;
            margin: 0 0 8px;
        }

        .meta {
            color: #4b5563;
            margin-bottom: 20px;
            font-size: 11px;
        }

        .respondent-block {
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 14px 16px;
            margin-bottom: 20px;
        }

        .respondent-header {
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 4px;
            color: #1f2937;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 6px;
            margin-bottom: 10px;
            break-after: avoid;
            page-break-after: avoid;
        }

        .respondent-meta {
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 8px;
        }

        .answer-row {
            display: flex;
            margin-bottom: 6px;
            padding: 4px 0;
            border-bottom: 1px solid #f3f4f6;
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .answer-label {
            font-weight: 600;
            width: 40%;
            padding-right: 8px;
            color: #374151;
        }

        .answer-value {
            width: 60%;
            color: #111827;
            word-break: break-word;
        }

        .no-answer {
            color: #9ca3af;
            font-style: italic;
        }

        .page-break {
            page-break-before: always;
        }

        .summary {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 20px;
            font-size: 11px;
            color: #4b5563;
        }

        .group-block {
            margin: 20px 0 12px;
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .group-block-first {
            margin-top: 0;
        }

        .group-header {
            font-size: 13px;
            font-weight: 700;
            color: #1f2937;
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 6px 10px;
            margin-bottom: 6px;
            break-inside: avoid;
            page-break-inside: avoid;
            break-after: avoid;
            page-break-after: avoid;
        }

        .group-description {
            font-size: 10px;
            color: #6b7280;
            margin: -2px 0 6px;
            padding: 0 2px;
        }
    </style>
</head>
<body>
<h1>{{ $form->name }}</h1>

<div class="summary">
    {{ __('filament-satisfaction-survey-builder::filament-resources.survey-form.pdf.responses.total_respondents') }} :
    <strong>{{ $entries->count() }}</strong>
</div>

@forelse ($entries as $index => $entry)
    @php
        $respondentName = $entry->user?->name ?? __('filament-satisfaction-survey-builder::filament-resources.survey-form.pdf.responses.guest');
        $respondentEmail = $entry->user?->email ?? null;
        $submittedAt = $entry->created_at?->format('Y-m-d H:i') ?? '—';
        $answers = is_array($entry->entry) ? $entry->entry : [];
    @endphp

    <div class="respondent-block">
        <div class="respondent-header">
            #{{ $index + 1 }} — {{ $respondentName }}
            @if ($respondentEmail)
                &lt;{{ $respondentEmail }}&gt;
            @endif
        </div>
        <div class="respondent-meta">
            {{ __('filament-satisfaction-survey-builder::filament-resources.survey-form.pdf.responses.submitted_at') }}
            : {{ $submittedAt }}
        </div>

        @if (empty($answers))
            <div class="no-answer">
                {{ __('filament-satisfaction-survey-builder::filament-resources.survey-form.pdf.responses.no_answers') }}
            </div>
        @else
            @php
                // Regroupe les réponses par catégorie (groupe), comme les Sections dans Show.php
                $groupedAnswers = [];
                $groupOrder = [];
                foreach ($answers as $position => $fieldEntry) {
                    // Les entrées stockent 'field_id' (id) + 'field' (label) ; 'field' seul = legacy
                    $rawFieldId = $fieldEntry['field_id'] ?? $fieldEntry['field'] ?? null;
                    $fieldIdKey = $rawFieldId !== null ? (string) $rawFieldId : null;
                    $group = ($fieldIdKey !== null && isset($fieldGroups[$fieldIdKey]))
                        ? $fieldGroups[$fieldIdKey]
                        : null;
                    $groupKey = $group ? 'group_' . $group['id'] : 'ungrouped';
                    if (! isset($groupedAnswers[$groupKey])) {
                        $groupedAnswers[$groupKey] = [
                            'name' => $group['name'] ?? null,
                            'description' => $group['description'] ?? null,
                            'items' => [],
                        ];
                        $groupOrder[] = $groupKey;
                    }
                    $groupedAnswers[$groupKey]['items'][] = ['entry' => $fieldEntry, 'position' => $position];
                }
                // Les groupes sans correspondance gardent l'ordre d'apparition (fin en pratique),
                // les groupes connus suivent l'ordre des Sections du formulaire
                $orderedGroupKeys = [];
                foreach (($formGroups ?? []) as $formGroup) {
                    $key = 'group_' . $formGroup['id'];
                    if (isset($groupedAnswers[$key])) {
                        $orderedGroupKeys[] = $key;
                    }
                }
                foreach ($groupOrder as $key) {
                    if (! in_array($key, $orderedGroupKeys, true)) {
                        $orderedGroupKeys[] = $key;
                    }
                }
            @endphp
            @foreach ($orderedGroupKeys as $groupKey)
                @php $groupData = $groupedAnswers[$groupKey]; @endphp
                <div class="group-block{{ $loop->first ? ' group-block-first' : '' }}">
                    @if ($groupData['name'])
                        <div class="group-header">{{ $groupData['name'] }}</div>
                        @if ($groupData['description'])
                            <div class="group-description">{{ $groupData['description'] }}</div>
                        @endif
                    @endif
                    @foreach ($groupData['items'] as $item)
                        @php
                            $fieldEntry = $item['entry'];
                            $rawFieldId = $fieldEntry['field_id'] ?? $fieldEntry['field'] ?? null;
                            $fieldIdKey = $rawFieldId !== null ? (string) $rawFieldId : null;
                            $fieldLabel = ($fieldIdKey !== null && isset($fields[$fieldIdKey]))
                                ? $fields[$fieldIdKey]
                                : ($fieldEntry['field'] ?? ($rawFieldId ? '- ' . $rawFieldId : '—'));
                            $answer = $fieldEntry['answer'] ?? null;

                            if (is_array($answer)) {
                                $displayAnswer = implode(', ', array_filter(array_map('strval', $answer)));
                            } elseif ($answer === null || $answer === '') {
                                $displayAnswer = null;
                            } elseif (is_bool($answer) || $answer === 'true' || $answer === 'false') {
                                $displayAnswer = $answer === 'true' ? 'Oui' : 'Non';
                            } else {
                                $displayAnswer = (string) $answer;
                            }
                        @endphp
                        <div class="answer-row">
                            <div class="answer-label">{{ $fieldLabel }}</div>
                            <div class="answer-value">
                                @if ($displayAnswer !== null && $displayAnswer !== '')
                                    {{ $displayAnswer }}
                                @else
                                    <span class="no-answer">—</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        @endif
    </div>
@empty
    <div class="summary">
        {{ __('filament-satisfaction-survey-builder::filament-resources.survey-form.pdf.responses.no_entries') }}
    </div>
@endforelse
</body>
</html>
