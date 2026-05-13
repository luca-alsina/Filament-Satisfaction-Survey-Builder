<?php

namespace Luca\FilamentSatisfactionSurveyBuilder\Filament\Resources\FilamentSatisfactionSurveyFormGroups\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class FilamentSatisfactionSurveyFormGroupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('order')
                    ->default(function () {
                        return $this->getOwnerRecord()->filamentFormGroups()->count() + 1;
                    })
                    ->numeric(),
                Textarea::make('description')
                    ->columnSpanFull(),
            ]);
    }
}
