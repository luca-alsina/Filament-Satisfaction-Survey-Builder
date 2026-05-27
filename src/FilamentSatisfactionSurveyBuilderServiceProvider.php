<?php

namespace Luca\FilamentSatisfactionSurveyBuilder;

use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Luca\FilamentSatisfactionSurveyBuilder\Filament\Pages\ShowEntry;
use Luca\FilamentSatisfactionSurveyBuilder\Filament\Pages\ShowForm;
use Luca\FilamentSatisfactionSurveyBuilder\Http\Middleware\SetFormPanel;
use Luca\FilamentSatisfactionSurveyBuilder\Livewire\FilamentForm\Form as FilamentForm;
use Luca\FilamentSatisfactionSurveyBuilder\Livewire\FilamentForm\Show as FilamentFormShow;
use Luca\FilamentSatisfactionSurveyBuilder\Livewire\FilamentFormUser\Show as FilamentFormUserShow;
use Luca\FilamentSatisfactionSurveyBuilder\Models\SurveyFormUser;
use Luca\FilamentSatisfactionSurveyBuilder\Observers\FilamentFormUserObserver;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentSatisfactionSurveyBuilderServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-satisfaction-survey-builder';

    protected array $styles = [
        'filament-satisfaction-survey-builder' => __DIR__ . '/../dist/filament-satisfaction-survey-builder.css',
    ];

    public function configurePackage(Package $package): void
    {
        $package->name('filament-satisfaction-survey-builder')
            ->hasMigration('create_dynamic_filament_form_tables')
            ->hasMigration('add_schema_to_filament_form_fields')
            ->hasMigration('add_notification_emails_to_filament_forms_table')
            ->hasMigration('change_label_to_text_in_filament_form_fields')
            ->hasMigration('add_private_entries_to_filament_forms_table')
            ->hasMigration('set_survey_form_entry_nullable')
            ->hasMigration('add_restricted_to_users_field')
            ->hasConfigFile('filament-satisfaction-survey-builder')
            ->hasViews('filament-satisfaction-survey-builder');
    }

    public function boot()
    {
        parent::boot();

        // Register the original components
        Livewire::component('luca.filament-satisfaction-survey-builder.livewire.filament-form.show', FilamentFormShow::class);
        Livewire::component('luca.filament-satisfaction-survey-builder.livewire.filament-form-user.show', FilamentFormUserShow::class);

        // Register the new layout components
        Livewire::component('luca.filament-satisfaction-survey-builder.livewire.filament-form.form', FilamentForm::class);

        // Register observer for form submission notifications
        SurveyFormUser::observe(FilamentFormUserObserver::class);

        // Register the form route globally so it's available when the model accesses it
        // The SetFormPanel middleware ensures the correct panel context is set based on authentication
        $middlewareClass = config('filament-satisfaction-survey-builder.set-form-panel-middleware-class');

        // Use package default middleware if not configured
        if (!$middlewareClass) {
            $middlewareClass = SetFormPanel::class;
        }

        $middleware = ['web', $middlewareClass];

        // Get the page classes (use package defaults if not configured)
        $formPageClass = config('filament-satisfaction-survey-builder.guest-panel-form-page-class')
            ?? ShowForm::class;
        $entryPageClass = config('filament-satisfaction-survey-builder.guest-panel-entry-page-class')
            ?? ShowEntry::class;

        Route::middleware($middleware)->group(function () use ($formPageClass, $entryPageClass) {
            Route::get(
                config('filament-satisfaction-survey-builder.filament-form-uri') . '/{form}',
                $formPageClass
            )->name('filament-satisfaction-survey-builder.show');

            Route::get(
                config('filament-satisfaction-survey-builder.filament-form-user-uri') . '/{entry}',
                $entryPageClass
            )->name('filament-form-users.show');
        });
    }

    public function packageBooted(): void
    {
        parent::packageBooted();
    }
}
