<?php

namespace Luca\FilamentSatisfactionSurveyBuilder\Filament\Resources\FilamentSatisfactionSurveyFormResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Luca\FilamentSatisfactionSurveyBuilder\Filament\Resources\FilamentSatisfactionSurveyFormResource\FilamentSatisfactionSurveyFormResource;
use Luca\FilamentSatisfactionSurveyBuilder\Models\SurveyForm;
use Luca\FilamentSatisfactionSurveyBuilder\Models\SurveyFormGroup;
use Luca\FilamentSatisfactionSurveyBuilder\Models\SurveyFormGroupField;

class CreateFilamentForm extends CreateRecord
{
    protected static string $resource = FilamentSatisfactionSurveyFormResource::class;

    public function getTitle(): string
    {
        return 'Create ' . config('filament-satisfaction-survey-builder.admin-panel-resource-name');
    }

    protected function afterCreate(): void
    {
        $record = $this->record;
        $templateId = $this->data['template_id'] ?? null;

        if ($templateId && $template = SurveyForm::find($templateId)) {
            $this->copyFromTemplate($record, $template);

            Notification::make()
                ->title(__('filament-satisfaction-survey-builder::filament-resources.survey-form.actions.duplicate_from_template_success'))
                ->success()
                ->send();
        }
    }

    protected function copyFromTemplate(SurveyForm $form, SurveyForm $template): void
    {
        $template->filamentFormGroups->each(function ($templateGroup) use ($form) {
            $group = SurveyFormGroup::create([
                'filament_form_id' => $form->id,
                'name' => $templateGroup->name,
                'order' => $templateGroup->order,
            ]);

            $templateGroup->filamentFormGroupFields->each(function ($templateField) use ($form, $group) {
                SurveyFormGroupField::create([
                    'filament_form_id' => $form->id,
                    'filament_form_group_id' => $group->id,
                    'label' => $templateField->label,
                    'type' => $templateField->type,
                    'required' => $templateField->required,
                    'order' => $templateField->order,
                    'hint' => $templateField->hint,
                    'options' => $templateField->options,
                    'rules' => $templateField->rules,
                ]);
            });
        });
    }
}
