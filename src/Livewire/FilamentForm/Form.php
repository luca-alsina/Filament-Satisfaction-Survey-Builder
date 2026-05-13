<?php

namespace Luca\FilamentSatisfactionSurveyBuilder\Livewire\FilamentForm;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Luca\FilamentSatisfactionSurveyBuilder\Models\SurveyForm;

#[Layout('components.layouts.app')]
class Form extends Component
{
    public SurveyForm $form;

    public function mount(SurveyForm $form)
    {
        $this->form = $form;
    }

    public function render()
    {
        /** @phpstan-ignore-next-line */
        return view('filament-satisfaction-survey-builder::livewire.filament-form.form');
    }
}
