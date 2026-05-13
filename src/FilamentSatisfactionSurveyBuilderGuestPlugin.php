<?php

namespace Luca\FilamentSatisfactionSurveyBuilder;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Illuminate\Support\Facades\Route;
use Luca\FilamentSatisfactionSurveyBuilder\Filament\Pages\ShowEntry;
use Luca\FilamentSatisfactionSurveyBuilder\Filament\Pages\ShowForm;

class FilamentSatisfactionSurveyBuilderGuestPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'filament-satisfaction-survey-builder-guest';
    }

    public function register(Panel $panel): void
    {
        // Register pages for viewing forms and entries in the guest panel
        $formPageClass = config('filament-satisfaction-survey-builder.guest-panel-form-page-class');
        $entryPageClass = config('filament-satisfaction-survey-builder.guest-panel-entry-page-class');

        // Use package defaults if not configured
        if (! $formPageClass) {
            $formPageClass = ShowForm::class;
        }

        if (! $entryPageClass) {
            $entryPageClass = ShowEntry::class;
        }

        $panel->pages([
            $formPageClass,
            $entryPageClass,
        ]);
    }

    public function boot(Panel $panel): void
    {
        // Entry route is registered in FilamentFormBuilderServiceProvider with SetFormPanel middleware
    }
}
