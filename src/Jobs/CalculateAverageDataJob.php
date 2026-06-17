<?php

namespace Luca\FilamentSatisfactionSurveyBuilder\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Luca\FilamentSatisfactionSurveyBuilder\Models\SurveyForm;

class CalculateAverageDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public SurveyForm $surveyForm
    )
    {
    }

    public function handle(): void
    {
        $averageData = [];

        // Get all fields that can be averaged
        $fields = $this->surveyForm->filamentFormFields
            ->filter(fn($field) => $field->type->canAverage() !== false && $field->type->canAverage() !== null && $field->type->canAverage() !== 0);

        // Get all entries for this form
        $entries = $this->surveyForm->filamentFormUsers
            ->whereNotNull('entry')
            ->values();

        foreach ($fields as $field) {
            $canAverage = $field->type->canAverage();
            $fieldId = $field->id;
            $averageData[$fieldId]['label'] = $field->label;
            $averageData[$fieldId]['field_type'] = $field->type->name;
            $averageData[$fieldId]['average_type'] = $canAverage;
            $averageData[$fieldId]['average'] = match ($canAverage) {
                // Average fill rate (TOGGLE, CHECKBOX)
                1 => $this->calculateFillRateAverage($entries, $fieldId),
                // Content average (STAR_RATING)
                2 => $this->calculateContentAverage($entries, $fieldId),
                // Average selection rate by option (SELECT, SELECT_MULTIPLE, CHECKBOX_LIST, RADIO)
                3 => $this->calculateSelectionRateByOption($entries, $fieldId, $field),
                default => null
            };
        }

//        \Log::info('Average Data: ', $averageData);

        // Update the survey form with the new average data
        $this->surveyForm->update([
            'average_data' => $averageData,
        ]);
    }

    protected function calculateFillRateAverage($entries, $fieldId): float
    {
        $entries = collect($entries);
        $totalEntries = $entries->count();

        if ($totalEntries === 0) {
            return 0.0;
        }

        $filledCount = 0;

        foreach ($entries as $entry) {
            $fieldEntry = collect($entry->entry)
                ->first(fn($item) => ($item['field_id'] ?? $item['field']) == $fieldId);

            if ($fieldEntry) {
                $answer = $fieldEntry['answer'] ?? null;
                // For boolean fields, check if it's a truthy value
                if ($answer === 'true' || $answer === true || $answer === 1 || $answer === '1') {
                    $filledCount++;
                }
            }
        }

        return round(($filledCount / $totalEntries) * 100, 2);
    }

    protected function calculateContentAverage($entries, $fieldId): float
    {
        $entries = collect($entries);
        $totalEntries = $entries->count();

        if ($totalEntries === 0) {
            return 0.0;
        }

        $sum = 0;
        $validCount = 0;

        foreach ($entries as $entry) {
            $fieldEntry = collect($entry->entry)
                ->first(fn($item) => ($item['field_id'] ?? $item['field']) == $fieldId);

            if ($fieldEntry) {
                $answer = $fieldEntry['answer'] ?? null;

                // For star rating, the answer is typically a numeric value
                if (is_numeric($answer)) {
                    $sum += (float)$answer;
                    $validCount++;
                } elseif (is_string($answer) && is_numeric($answer)) {
                    $sum += (float)$answer;
                    $validCount++;
                }
            }
        }

        if ($validCount === 0) {
            return 0.0;
        }

        return round($sum / $validCount, 2);
    }

    protected function calculateSelectionRateByOption($entries, $fieldId, $field): array
    {
        $entries = collect($entries);
        $totalEntries = $entries->count();
        $options = $field->options ?? [];

        if ($totalEntries === 0 || empty($options)) {
            return [];
        }

        $optionCounts = array_fill_keys(array_keys($options), 0);

        foreach ($entries as $entry) {
            $fieldEntry = collect($entry->entry)
                ->first(fn($item) => ($item['field_id'] ?? $item['field']) == $fieldId);

            if ($fieldEntry) {
                $answer = $fieldEntry['answer'] ?? null;

                if ($answer === null) {
                    continue;
                }

                // For SELECT, RADIO: answer is a single key
                if (is_string($answer) || is_numeric($answer)) {
                    if (isset($optionCounts[$answer])) {
                        $optionCounts[$answer]++;
                    }
                }

                // For SELECT_MULTIPLE, CHECKBOX_LIST: answer is an array of keys
                if (is_array($answer)) {
                    foreach ($answer as $selectedOption) {
                        if (isset($optionCounts[$selectedOption])) {
                            $optionCounts[$selectedOption]++;
                        }
                    }
                }
            }
        }

        // Calculate percentages for each option
        $result = [];
        foreach ($optionCounts as $optionKey => $count) {
            $result[$optionKey] = round(($count / $totalEntries) * 100, 2);
        }

        return $result;
    }
}
