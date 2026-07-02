<div class="fb-form-component filament-form-builder">
    <div class="w-full fb-form-container">
        @if ($this->filamentForm->description)
            <div class="mb-4 prose prose-sm max-w-none dark:prose-invert">
                {{-- Description is admin-controlled rich text (HTML) --}}
                {!! $this->filamentForm->description !!}
            </div>
        @endif
        <form wire:submit="create">
            @csrf
            {{ $this->form }}

            <x-filament::button type="submit" style="margin-top: 1rem" :size="\Filament\Support\Enums\Size::Large"
                                color="success" class="w-full">
                {{ __('filament-satisfaction-survey-builder::views.livewire.filament-form.show.submit') }}
            </x-filament::button>
        </form>

        <x-filament-actions::modals/>
    </div>
</div>
