<?php

namespace Luca\FilamentSatisfactionSurveyBuilder\Filament\Resources\FilamentSatisfactionSurveyFormGroups\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FilamentSatisfactionSurveyFormGroupsTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('survey_form_id'),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('description'),

                TextColumn::make('order'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
