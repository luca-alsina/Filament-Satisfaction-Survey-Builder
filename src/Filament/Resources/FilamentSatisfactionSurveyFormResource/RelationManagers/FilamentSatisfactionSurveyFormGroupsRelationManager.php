<?php

namespace Luca\FilamentSatisfactionSurveyBuilder\Filament\Resources\FilamentSatisfactionSurveyFormResource\RelationManagers;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class FilamentSatisfactionSurveyFormGroupsRelationManager extends RelationManager
{
    protected static string $relationship = 'filamentFormGroups';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __(config('filament-satisfaction-survey-builder.admin-panel-filament-form-group-name-plural'));
    }

    public function form(Schema $schema): Schema
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

    public function table(Table $table): Table
    {
        $form = $this->getOwnerRecord();

        return $table
            ->recordTitleAttribute('name')
            ->heading(config('filament-satisfaction-survey-builder.admin-panel-filament-form-group-name-plural'))
            ->modelLabel(config('filament-satisfaction-survey-builder.admin-panel-filament-form-group-name'))
            ->reorderable('order')
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('order')
                    ->numeric()
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->visible(function () use ($form) {
                        return !$form->locked;
                    })
                    ->label('Create ' . config('filament-satisfaction-survey-builder.admin-panel-filament-form-group-name')),
                /*                Action::make('lock_fields')
                                    ->label(__('Lock ' . config('filament-satisfaction-survey-builder.admin-panel-filament-form-group-name-plural')))
                                    ->requiresConfirmation()
                                    ->modalHeading('Lock Form Fields. Doing this will lock the forms fields and new fields will no longer be able to be changed or edited')
                                    ->visible(function () use ($form) {
                                        return !$form->locked;
                                    })
                                    ->action(function () use ($form) {
                                        $form->update([
                                            'locked' => true,
                                        ]);
                                    }),
                                Action::make('unlock_fields')
                                    ->label(__('Unlock ' . config('filament-satisfaction-survey-builder.admin-panel-filament-form-group-name-plural')))
                                    ->requiresConfirmation()
                                    ->modalHeading('Unlock Form Fields. Changing fields after entries has been made can cause inconsistencies for prexisting entries')
                                    ->visible(function () use ($form) {
                                        return $form->locked;
                                    })
                                    ->action(function () use ($form) {
                                        $form->update([
                                            'locked' => false,
                                        ]);
                                    }),*/
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->visible(function () use ($form) {
                            return !$form->locked;
                        }),
                    DeleteAction::make()
                        ->visible(function () use ($form) {
                            return !$form->locked;
                        }),
                ]),
            ], position: RecordActionsPosition::BeforeColumns)
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(function () use ($form) {
                            return !$form->locked;
                        }),
                ]),
            ]);
    }
}
