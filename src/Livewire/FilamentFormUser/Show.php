<?php

namespace Luca\FilamentSatisfactionSurveyBuilder\Livewire\FilamentFormUser;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Livewire\Component;
use Luca\FilamentSatisfactionSurveyBuilder\Enums\FilamentFieldTypeEnum;
use Luca\FilamentSatisfactionSurveyBuilder\Models\SurveyFormGroup;
use Luca\FilamentSatisfactionSurveyBuilder\Models\SurveyFormUser;

class Show extends Component implements HasActions, HasForms, HasInfolists
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithInfolists;

    public SurveyFormUser $entry;

    public function mount(SurveyFormUser $entry): void
    {
        $this->entry = $entry->load(
            'user',
            'filamentForm.filamentFormGroups.filamentFormGroupFields',
        );
    }

    public function entryInfoList(Schema $schema): Schema
    {
        $components = [
            TextEntry::make('user.name')
                ->label('Name')
                ->visible(fn () => $this->entry->user_id !== null),
            TextEntry::make('filamentForm.name')
                ->label('Form Name'),
            TextEntry::make('created_at')
                ->label(fn () => $this->entry->entry ? 'Form Completed At' : 'User added At')
                ->dateTime(),
            TextEntry::make('updated_at')
                ->visible(fn () => $this->entry->entry && ($this->entry->updated_at !== $this->entry->created_at))
                ->label('Form Completed At')
                ->dateTime(),
        ];

        if ($this->entry->entry) {
            $components = array_merge($components, $this->getAnswerSections());
        }

        $components[] = RepeatableEntry::make('media')
            ->label('Uploaded Files')
            ->schema([
                TextEntry::make('custom_properties.field_label')
                    ->label('Question'),
                TextEntry::make('custom_properties.original_name')
                    ->label('File Name')
                    ->suffixAction(
                        Action::make('download')
                            ->icon('heroicon-o-arrow-down-tray')
                            ->action(function ($record) {
                                return response()->download(
                                    $record->getPath(),
                                    $record->getCustomProperty('original_name')
                                );
                            })
                    ),
            ])
            ->state(function () {
                return $this->entry->getMedia();
            })
            ->visible(fn () => $this->entry->getMedia()->isNotEmpty());

        return $schema
            ->record($this->entry)
            ->schema($components);
    }

    /**
     * @return array<int, Section>
     */
    private function getAnswerSections(): array
    {
        $answers = is_array($this->entry->entry) ? $this->entry->entry : [];
        $sections = [];

        foreach ($this->entry->filamentForm->filamentFormGroups as $group) {
            $groupAnswers = $this->getAnswersForGroup($group, $answers);

            if ($groupAnswers === []) {
                continue;
            }

            $sections[] = Section::make($group->name)
                ->description($group->description)
                ->schema([
                    KeyValueEntry::make('answers_'.$group->id)
                        ->label(null)
                        ->keyLabel('Question')
                        ->valueLabel('Answer')
                        ->state($groupAnswers),
                ]);
        }

        return $sections;
    }

    /**
     * @param  array<int, mixed>  $answers
     * @return array<string, mixed>
     */
    private function getAnswersForGroup(SurveyFormGroup $group, array $answers): array
    {
        $fieldIds = [];
        $fieldLabels = [];
        $repeaterPrefixes = [];

        foreach ($group->filamentFormGroupFields as $field) {
            $fieldIds[] = (string) $field->id;
            $fieldLabels[] = (string) $field->label;

            if ($field->type === FilamentFieldTypeEnum::REPEATER) {
                $repeaterPrefixes[] = str_replace(' ', '_', strtolower($field->label)).'_';
            }
        }

        $groupAnswers = [];

        foreach ($answers as $answer) {
            if (! is_array($answer) || ! array_key_exists('field', $answer)) {
                continue;
            }

            $fieldId = (string) ($answer['field_id'] ?? '');
            $isGroupAnswer = in_array($fieldId, $fieldIds, true)
                || in_array((string) $answer['field'], $fieldLabels, true);

            if (! $isGroupAnswer) {
                foreach ($repeaterPrefixes as $repeaterPrefix) {
                    if (str_starts_with($fieldId, $repeaterPrefix)) {
                        $isGroupAnswer = true;

                        break;
                    }
                }
            }

            if ($isGroupAnswer) {
                $groupAnswers[(string) $answer['field']] = $answer['answer'] ?? null;
            }
        }

        return $groupAnswers;
    }

    public function render()
    {
        /** @phpstan-ignore-next-line */
        return view('filament-satisfaction-survey-builder::livewire.filament-form-user.show');
    }
}
