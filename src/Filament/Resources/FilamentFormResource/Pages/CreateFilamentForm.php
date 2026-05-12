<?php

namespace Luca\FilamentSatisfactionSurveyBuilder\Filament\Resources\FilamentFormResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Luca\FilamentSatisfactionSurveyBuilder\Filament\Resources\FilamentFormResource;

class CreateFilamentForm extends CreateRecord
{
    protected static string $resource = FilamentFormResource::class;

    public function getTitle(): string
    {
        return 'Create '.config('filament-form-builder.admin-panel-resource-name');
    }
}
