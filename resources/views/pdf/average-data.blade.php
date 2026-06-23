<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $form->name }} - Average Statistics</title>
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
    </style>
</head>
<body>
<h1>{{ $form->name }}</h1>
<div class="meta">
    {{ __('filament-satisfaction-survey-builder::filament-resources.survey-form.sections.average_data') }}
    - {{ $generatedAt->format('Y-m-d H:i') }}
</div>

@foreach ($averageDataRows as $row)
    @php
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
            @php
                $options = is_array($average['result'] ?? null) ? $average['result'] : [];
            @endphp
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
@endforeach
</body>
</html>
