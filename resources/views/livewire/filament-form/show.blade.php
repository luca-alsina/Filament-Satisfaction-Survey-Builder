<div class="fb-form-component filament-form-builder">
    <div class="w-full fb-form-container">
        @if ($this->filamentForm->is_template)
            <div
                class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg dark:bg-yellow-900/20 dark:border-yellow-800">
                <p class="text-yellow-800 dark:text-yellow-200">
                    {{ __('filament-satisfaction-survey-builder::views.livewire.filament-form.show.template_message') }}
                </p>
            </div>
        @elseif ($this->hasAlreadySubmitted)
            <div
                class="p-4 bg-danger-50 border border-danger-200 rounded-lg dark:bg-danger-900/20 dark:border-danger-800">
                <p class="font-semibold text-danger-800 dark:text-danger-200">
                    {{ __('filament-satisfaction-survey-builder::views.livewire.filament-form.show.already_submitted_title') }}
                </p>
                <p class="mt-1 text-sm text-danger-700 dark:text-danger-300">
                    {{ __('filament-satisfaction-survey-builder::views.livewire.filament-form.show.already_submitted_message') }}
                </p>
                <div class="mt-3">
                    <x-filament::button tag="a" :href="$this->existingEntryUrl" color="danger" icon="heroicon-o-eye">
                        {{ __('filament-satisfaction-survey-builder::views.livewire.filament-form.show.view_answers') }}
                    </x-filament::button>
                </div>
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
