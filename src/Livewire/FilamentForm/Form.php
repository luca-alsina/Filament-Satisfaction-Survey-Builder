<?php

namespace Luca\FilamentSatisfactionSurveyBuilder\Livewire\FilamentForm;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Luca\FilamentSatisfactionSurveyBuilder\Models\FilamentForm;

#[Layout('components.layouts.app')]
class Form extends Component
{
    public FilamentForm $form;

    public function mount(FilamentForm $form)
    {
        $this->form = $form;
    }

    public function render()
    {
        /** @phpstan-ignore-next-line */
        return view('filament-form-builder::livewire.filament-form.form');
    }
}
