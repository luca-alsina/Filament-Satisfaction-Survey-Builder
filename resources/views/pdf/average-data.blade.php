<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>{{ $form->name }} - {{ $documentTitle ?? __('filament-satisfaction-survey-builder::filament-resources.survey-form.sections.average_data') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #111827;
            margin: 24px;
        }

        h1 {
            font-size: 20px;
            margin: 0 0 8px;
        }

        .meta {
            color: #4b5563;
            margin-bottom: 20px;
        }

        .card {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 12px;
            page-break-inside: avoid;
        }

        .row-title {
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            font-size: 10px;
            color: #374151;
            margin-left: 6px;
        }

        .list {
            margin: 6px 0 0 0;
            padding-left: 16px;
        }

        .muted {
            color: #6b7280;
            font-size: 11px;
            margin-top: 4px;
        }

        .summary {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 20px;
            font-size: 12px;
            color: #374151;
        }

        .summary strong {
            color: #111827;
        }

        h2 {
            font-size: 16px;
            margin: 26px 0 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #e5e7eb;
            color: #1f2937;
            page-break-after: avoid;
        }

        .group-description {
            color: #6b7280;
            font-size: 10px;
            margin: -4px 0 8px;
        }

        .text-question {
            margin: 10px 0 14px;
            padding: 0 0 10px;
            border-bottom: 1px solid #e5e7eb;
        }

        .text-answer-list {
            margin: 6px 0 0;
            padding: 0;
            list-style: none;
        }

        .text-answer {
            margin: 5px 0;
            padding: 7px 10px;
            border-left: 2px solid #d1d5db;
            background: #f9fafb;
            white-space: pre-wrap;
            overflow-wrap: break-word;
            page-break-inside: avoid;
        }

        .no-answer {
            color: #9ca3af;
            font-style: italic;
        }
    </style>
</head>
<body>
<h1>{{ $form->name }}</h1>
<div class="meta">
    {{ $documentTitle ?? __('filament-satisfaction-survey-builder::filament-resources.survey-form.sections.average_data') }}
    - {{ $generatedAt->format('Y-m-d H:i') }}
</div>

<div class="summary">
    <strong>{{ __('filament-satisfaction-survey-builder::filament-resources.survey-form.pdf.average.response_rate') }} :</strong>
    @if ($responseRate !== null)
        {{ number_format((float) $responseRate, 2) }}%
    @else
        —
    @endif
    <span class="muted">
        ({{ (int) $totalResponses }} / {{ (int) $totalRegistered }}
        {{ __('filament-satisfaction-survey-builder::filament-resources.survey-form.pdf.average.responses_per_registered') }})
    </span>
</div>

@php
    $averageGroups = $questionGroups ?? [[
        'name' => null,
        'description' => null,
        'questions' => collect($averageDataRows)
            ->map(fn (array $row): array => ['type' => 'average', 'row' => $row])
            ->all(),
    ]];
@endphp

@if (empty($averageDataRows))
    <div class="muted">{{ __('filament-satisfaction-survey-builder::filament-resources.survey-form.pdf.responses.no_statistics') }}</div>
@endif

@foreach ($averageGroups as $group)
    @if (!empty($group['questions']))
        @if (!empty($group['name']))
            <h2>{{ $group['name'] }}</h2>
            @if (!empty($group['description']))
                <div class="group-description">{{ $group['description'] }}</div>
            @endif
        @endif

        @foreach ($group['questions'] as $question)
            @if (($question['type'] ?? null) === 'average')
                @php
                    $row = $question['row'] ?? [];
                    $label = (string) ($row['label'] ?? ('#' . ($row['field_id'] ?? '')));
                    $fieldType = $row['field_type'] ?? null;
                    $averageType = $row['average_type'] ?? null;
                    $average = is_array($row['average'] ?? null) ? $row['average'] : [];
                @endphp

                <div class="card">
                    <div class="row-title">
                        {{ $label }}
                        @if ($fieldType)
                            <span class="badge">{{ $fieldType }}</span>
                        @endif
                    </div>

                    @if ($averageType === 1)
                        <div>
                            <strong>{{ __('filament-satisfaction-survey-builder::filament-resources.survey-form.average_data.fill_rate') }}:</strong>
                            {{ number_format((float) ($average['result'] ?? 0), 2) }}%
                        </div>
                        <div class="muted">
                            {{ (int) ($average['filled_count'] ?? 0) }} / {{ (int) ($average['total_entries'] ?? 0) }}
                            {{ __('filament-satisfaction-survey-builder::filament-resources.survey-form.average_data.entries') }}
                        </div>
                    @elseif ($averageType === 2)
                        <div>
                            <strong>{{ __('filament-satisfaction-survey-builder::filament-resources.survey-form.average_data.average_value') }}:</strong>
                            {{ number_format((float) ($average['result'] ?? 0), 2) }}
                        </div>
                        <div class="muted">
                            {{ (int) ($average['valid_count'] ?? 0) }} / {{ (int) ($average['total_entries'] ?? 0) }}
                            {{ __('filament-satisfaction-survey-builder::filament-resources.survey-form.average_data.valid_entries') }}
                        </div>
                    @elseif ($averageType === 3)
                        @php $options = is_array($average['result'] ?? null) ? $average['result'] : []; @endphp
                        @if (!empty($options))
                            <ul class="list">
                                @foreach ($options as $option => $percent)
                                    <li>{{ $option }}: {{ number_format((float) $percent, 2) }}%</li>
                                @endforeach
                            </ul>
                        @else
                            <div>{{ __('filament-satisfaction-survey-builder::filament-resources.survey-form.average_data.no_option_data') }}</div>
                        @endif
                        <div class="muted">
                            {{ (int) ($average['total_entries'] ?? 0) }}
                            {{ __('filament-satisfaction-survey-builder::filament-resources.survey-form.average_data.total_entries') }}
                        </div>
                    @else
                        <div>{{ __('filament-satisfaction-survey-builder::filament-resources.survey-form.average_data.unsupported') }}</div>
                    @endif
                </div>
            @elseif (($question['type'] ?? null) === 'text')
                @php $answers = $textAnswersByFieldId[$question['id']] ?? []; @endphp
                <div class="text-question">
                    <div class="row-title">{{ $question['label'] }}</div>
                    @if (!empty($answers))
                        <ul class="text-answer-list">
                            @foreach ($answers as $answer)
                                <li class="text-answer">{{ $answer }}</li>
                            @endforeach
                        </ul>
                    @else
                        <div class="muted">{{ __('filament-satisfaction-survey-builder::filament-resources.survey-form.pdf.synthesis.no_text_responses') }}</div>
                    @endif
                </div>
            @endif
        @endforeach
    @endif
@endforeach
</body>
</html>
