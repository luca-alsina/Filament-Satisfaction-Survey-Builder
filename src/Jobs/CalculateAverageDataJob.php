<?php

namespace Luca\FilamentSatisfactionSurveyBuilder\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Luca\FilamentSatisfactionSurveyBuilder\Enums\FilamentFieldTypeEnum;
use Luca\FilamentSatisfactionSurveyBuilder\Models\SurveyForm;
use Luca\FilamentSatisfactionSurveyBuilder\Models\SurveyFormGroupField;
use Luca\FilamentSatisfactionSurveyBuilder\Models\SurveyFormUser;

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
        $averageData = $this->calculate();

        $this->surveyForm->update(['average_data' => $averageData]);
    }

    public function calculate(): array
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
                1 => $this->calculateFillRateAverage($entries, $field),
                // Content average (STAR_RATING)
                2 => $this->calculateContentAverage($entries, $field),
                // Average selection rate by option (SELECT, SELECT_MULTIPLE, CHECKBOX_LIST, RADIO)
                3 => $this->calculateSelectionRateByOption($entries, $field),
                default => null
            };
        }

        return $averageData;
    }

    protected function calculateFillRateAverage($entries, SurveyFormGroupField $field): array
    {
        $entries = collect($entries);
        $totalEntries = $entries->count();

        if ($totalEntries === 0) {
            return [
                'result' => 0.0,
                'total_entries' => $totalEntries,
                'filled_count' => 0
            ];
        }

        $filledCount = 0;

        foreach ($entries as $entry) {
            $fieldEntry = $this->findFieldEntry($entry, $field);

            if ($fieldEntry) {
                $answer = $fieldEntry['answer'] ?? null;
                // For boolean fields, check if it's a truthy value
                if ($answer === 'true' || $answer === true || $answer === 1 || $answer === '1') {
                    $filledCount++;
                }
            }
        }

        return [
            'result' => round(($filledCount / $totalEntries) * 100, 2),
            'total_entries' => $totalEntries,
            'filled_count' => $filledCount
        ];
    }

    protected function calculateContentAverage($entries, SurveyFormGroupField $field): array
    {
        $entries = collect($entries);
        $totalEntries = $entries->count();

        if ($totalEntries === 0) {
            return [
                'result' => 0.0,
                'total_entries' => $totalEntries,
                'filled_count' => 0
            ];
        }

        $sum = 0;
        $validCount = 0;

        foreach ($entries as $entry) {
            $fieldEntry = $this->findFieldEntry($entry, $field);

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
            return [
                'result' => 0.0,
                'total_entries' => $totalEntries,
                'valid_count' => $validCount,
                'sum' => $sum
            ];
        }

        return [
            'result' => round($sum / $validCount, 2),
            'total_entries' => $totalEntries,
            'valid_count' => $validCount,
            'sum' => $sum
        ];
    }

    protected function calculateSelectionRateByOption(\Illuminate\Database\Eloquent\Collection $entries, SurveyFormGroupField $field): array
    {
        /** @var Collection<SurveyFormUser> $entries */
        $entries = collect($entries);
        $totalEntries = $entries->count();
        $options = $field->options ?? [];

        if ($totalEntries === 0 || empty($options)) {
            return [
                'result' => [],
                'total_entries' => $totalEntries
            ];
        }


        $optionCounts = array_fill_keys(array_values($options), 0);


        foreach ($entries as $entry) {

            $fieldEntry = $this->findFieldEntry($entry, $field);

            if ($fieldEntry) {
                $answer = $fieldEntry['answer'] ?? null;


                if ($answer === null) {
                    continue;
                }

                // Current submissions store labels, with multiple labels joined by comma.
                // Older submissions may contain option keys or arrays instead.
                if (is_array($answer)) {
                    $selected = $answer;
                } elseif (in_array($field->type, [FilamentFieldTypeEnum::SELECT_MULTIPLE, FilamentFieldTypeEnum::CHECKBOX_LIST], true)
                    && ! array_key_exists((string) $answer, $optionCounts)) {
                    $selected = explode(', ', (string) $answer);
                } else {
                    $selected = [$answer];
                }

                $matched = [];
                foreach ($selected as $option) {
                    if (! is_string($option) && ! is_numeric($option)) {
                        continue;
                    }
                    $label = array_key_exists($option, $optionCounts) ? $option : ($options[$option] ?? null);
                    if ($label !== null && isset($optionCounts[$label])) {
                        $matched[$label] = true;
                    }
                }
                foreach (array_keys($matched) as $label) {
                    $optionCounts[$label]++;
                }
            }
        }

        // Calculate percentages for each option
        $result = [
            'total_entries' => $totalEntries
        ];
        foreach ($optionCounts as $optionKey => $count) {
            $result['result'][$optionKey] = round(($count / $totalEntries) * 100, 2);
        }

        return $result;
    }

    private function findFieldEntry(SurveyFormUser $entry, SurveyFormGroupField $field): ?array
    {
        return collect(is_array($entry->entry) ? $entry->entry : [])
            ->first(fn ($item) => is_array($item) && (
                isset($item['field_id'])
                    ? (string) $item['field_id'] === (string) $field->id
                    : (string) ($item['field'] ?? '') === (string) $field->label
            ));
    }
}
