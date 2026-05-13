<?php

namespace Luca\FilamentSatisfactionSurveyBuilder;

use Filament\Contracts\Plugin;
use Filament\Panel;

class FilamentSatisfactionSurveyBuilderPlugin implements Plugin
{
    protected array $styles = [
        'filament-satisfaction-survey-builder' => __DIR__.'/../dist/filament-satisfaction-survey-builder.css',
    ];

    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'filament-satisfaction-survey-builder';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources(
                config('filament-satisfaction-survey-builder.resources')
            );
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
