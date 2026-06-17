@php
    $rows = is_array($averageDataRows ?? null) ? $averageDataRows : [];
@endphp

@if (empty($rows))
    <p class="text-sm text-gray-500 dark:text-gray-400">
        {{ __('filament-satisfaction-survey-builder::filament-resources.survey-form.sections.average_data_empty') }}
    </p>
@else
    <div class="space-y-4">
        @foreach ($rows as $row)
            @php
                $label = (string) ($row['label'] ?? ('#' . ($row['field_id'] ?? '')));
                $fieldType = $row['field_type'] ?? null;
                $averageType = $row['average_type'] ?? null;
                $average = is_array($row['average'] ?? null) ? $row['average'] : [];
            @endphp

            <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                <div class="flex flex-wrap items-center gap-2">
                    <p class="text-sm font-bold underline text-gray-900 dark:text-gray-100">{{ $label }}</p>
                </div>
                {{--
                                @if ($fieldType)
                                    <span
                                        class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-xs text-gray-700 dark:bg-gray-800 dark:text-gray-200">
                                            {{ $fieldType }}
                                        </span>
                                @endif
                --}}

                <div class="mt-3 text-sm text-gray-700 dark:text-gray-300">
                    @if ($averageType === 1)
                        <p>
                            <span class="font-medium">{{ __('filament-satisfaction-survey-builder::filament-resources.survey-form.average_data.fill_rate') }}:</span>
                            {{ number_format((float) ($average['result'] ?? 0), 2) }}%
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ (int) ($average['filled_count'] ?? 0) }} / {{ (int) ($average['total_entries'] ?? 0) }}
                            {{ __('filament-satisfaction-survey-builder::filament-resources.survey-form.average_data.entries') }}
                        </p>
                    @elseif ($averageType === 2)
                        <p>
                            <span class="font-medium">{{ __('filament-satisfaction-survey-builder::filament-resources.survey-form.average_data.average_value') }}:</span>
                            {{ number_format((float) ($average['result'] ?? 0), 2) }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ (int) ($average['valid_count'] ?? 0) }} / {{ (int) ($average['total_entries'] ?? 0) }}
                            {{ __('filament-satisfaction-survey-builder::filament-resources.survey-form.average_data.valid_entries') }}
                        </p>
                    @elseif ($averageType === 3)
                        @php
                            $options = is_array($average['result'] ?? null) ? $average['result'] : [];
                        @endphp
                        @if (!empty($options))
                            <ul class="space-y-1">
                                @foreach ($options as $option => $percent)
                                    <li>
                                        <span class="font-medium">{{ $option }}:</span>
                                        {{ number_format((float) $percent, 2) }}%
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p>{{ __('filament-satisfaction-survey-builder::filament-resources.survey-form.average_data.no_option_data') }}</p>
                        @endif
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            {{ (int) ($average['total_entries'] ?? 0) }}
                            {{ __('filament-satisfaction-survey-builder::filament-resources.survey-form.average_data.total_entries') }}
                        </p>
                    @else
                        <p>{{ __('filament-satisfaction-survey-builder::filament-resources.survey-form.average_data.unsupported') }}</p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endif
