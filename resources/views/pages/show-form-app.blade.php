<x-filament-panels::page>
    @livewire('luca.filament-satisfaction-survey-builder.livewire.filament-form.show', ['form' => $this->form, 'token' => $this->token ?? request()->route('token') ?? request()->query('token')], key('form-'.$this->form->id.'-'.($this->token ?? request()->route('token') ?? request()->query('token') ?? 'no-token')))
</x-filament-panels::page>
