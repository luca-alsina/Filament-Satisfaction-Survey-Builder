<div class="fb-form-component filament-form-builder">
    <div class="w-full fb-form-container">
        @if ($this->filamentForm->is_template)
            <div
                class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg dark:bg-yellow-900/20 dark:border-yellow-800">
                <p class="text-yellow-800 dark:text-yellow-200">
                    {{ __('filament-satisfaction-survey-builder::views.livewire.filament-form.show.template_message') }}
                </p>
            </div>
        @else
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
        @endif
    </div>
</div>
