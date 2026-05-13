<?php

namespace Luca\FilamentSatisfactionSurveyBuilder\Filament\Resources\FilamentSatisfactionSurveyFormGroups\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class FilamentSatisfactionSurveyFormGroupInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id'),

                TextEntry::make('survey_form_id'),

                TextEntry::make('name'),

                TextEntry::make('description'),

                TextEntry::make('order'),
            ]);
    }
}
