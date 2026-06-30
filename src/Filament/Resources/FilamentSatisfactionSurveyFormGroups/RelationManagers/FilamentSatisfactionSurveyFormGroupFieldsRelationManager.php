<?php

namespace Luca\FilamentSatisfactionSurveyBuilder\Filament\Resources\FilamentSatisfactionSurveyFormGroups\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Luca\FilamentSatisfactionSurveyBuilder\Enums\FilamentFieldTypeEnum;

class FilamentSatisfactionSurveyFormGroupFieldsRelationManager extends RelationManager
{
    protected static string $relationship = 'filamentFormGroupFields';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('filament-satisfaction-survey-builder::filament-resources.survey-form-group-fields.name.plural');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
                    ->label(__('filament-satisfaction-survey-builder::filament-resources.survey-form-group-fields.fields.type'))
                    ->options(function () {
                        return collect(FilamentFieldTypeEnum::cases())
                            ->mapWithKeys(fn($type) => [$type->name => $type->getLabel()])
                            ->sortBy(fn($label, $key) => $label)
                            ->toArray();
                    })
                    ->columnSpan(function ($state) {
                        if (!empty($state) && FilamentFieldTypeEnum::fromString($state)->hasOptions()) {
                            return 1;
                        }

                        return 2;
                    })
                    ->required()
                    ->live(),
                Textarea::make('label')
                    ->label(__('filament-satisfaction-survey-builder::filament-resources.survey-form-group-fields.fields.label'))
                    ->required()
                    ->label(function (Get $get) {
                        return $get('type') === FilamentFieldTypeEnum::HEADING->name ? 'Heading' : 'Label';
                    }),
                TagsInput::make('options')
                    ->label(__('filament-satisfaction-survey-builder::filament-resources.survey-form-group-fields.fields.options'))
                    ->placeholder('Add options')
                    ->hint('Press enter after inputting each option')
                    ->visible(function (Get $get) {
                        if ($get('type')) {
                            return FilamentFieldTypeEnum::fromString($get('type'))->hasOptions();
                        }

                        return false;
                    }),
                Textarea::make('hint')
                    ->label(function (Get $get) {
                        return $get('type') === FilamentFieldTypeEnum::HEADING->name ? __('filament-satisfaction-survey-builder::filament-resources.survey-form-group-fields.fields.subheading') : __('filament-satisfaction-survey-builder::filament-resources.survey-form-group-fields.fields.hint');
                    }),
                // TagsInput::make('rules')
                //     ->placeholder('Add rules')
                //     ->hint('view list of available rules here, https://laravel.com/docs/11.x/validation#available-validation-rules')
                //     ->visible(function (Get $get) {
                //         return $get('type') !== FilamentFieldTypeEnum::REPEATER->name
                //             && $get('type') !== FilamentFieldTypeEnum::HEADING->name;
                //     }),
                TextInput::make('order')
                    ->label(__('filament-satisfaction-survey-builder::filament-resources.survey-form-group-fields.fields.order'))
                    ->default(function () {
                        return $this->getOwnerRecord()->filamentFormGroupFields()->count() + 1;
                    })
                    ->numeric(),
                Toggle::make('required')
                    ->label(__('filament-satisfaction-survey-builder::filament-resources.survey-form-group-fields.fields.required'))
                    ->visible(function (Get $get) {
                        return $get('type') !== FilamentFieldTypeEnum::REPEATER->name
                            && $get('type') !== FilamentFieldTypeEnum::HEADING->name;
                    }),
                Toggle::make('average')
                    ->label(__('filament-satisfaction-survey-builder::filament-resources.survey-form-group-fields.fields.average'))
                    ->visible(function (Get $get) {
                        return FilamentFieldTypeEnum::fromString($get('type') ?? '')?->canAverage() ?? false;
                    }),
                Repeater::make('schema')
                    ->label(__('filament-satisfaction-survey-builder::filament-resources.survey-form-group-fields.fields.schema'))
                    ->schema([
                        Textarea::make('label')
                            ->label(__('filament-satisfaction-survey-builder::filament-resources.survey-form-group-fields.fields.label'))
                            ->required(),
                        Select::make('type')
                            ->label(__('filament-satisfaction-survey-builder::filament-resources.survey-form-group-fields.fields.type'))
                            ->options(function () {
                                $options = collect(FilamentFieldTypeEnum::cases())
                                    ->filter(fn($type) => $type !== FilamentFieldTypeEnum::REPEATER)
                                    ->mapWithKeys(fn($type) => [$type->name => $type->fieldName()])
                                    ->toArray();

                                return $options;
                            })
                            ->required()
                            ->live(),
                        TagsInput::make('options')
                            ->label(__('filament-satisfaction-survey-builder::filament-resources.survey-form-group-fields.fields.options'))
                            ->placeholder('Add options')
                            ->hint('Press enter after inputting each option')
                            ->visible(function (Get $get) {
                                if ($get('type')) {
                                    return FilamentFieldTypeEnum::fromString($get('type'))->hasOptions();
                                }

                                return false;
                            }),
                        Textarea::make('hint')
                            ->label(__('filament-satisfaction-survey-builder::filament-resources.survey-form-group-fields.fields.hint')),
                        // TagsInput::make('rules')
                        //     ->placeholder('Add rules')
                        //     ->hint('view list of available rules here, https://laravel.com/docs/11.x/validation#available-validation-rules'),
                        Toggle::make('required')
                            ->label(__('filament-satisfaction-survey-builder::filament-resources.survey-form-group-fields.fields.required')),
                    ])
                    ->columns(2)
                    ->columnSpanFull()
                    ->visible(function (Get $get) {
                        return $get('type') === FilamentFieldTypeEnum::REPEATER->name;
                    }),
            ]);
    }

    public function table(Table $table): Table
    {
        $form = $this->getOwnerRecord();

        return $table
            ->recordTitleAttribute('label')
            ->heading(__('filament-satisfaction-survey-builder::filament-resources.survey-form-group-fields.name.plural'))
            ->modelLabel(__('filament-satisfaction-survey-builder::filament-resources.survey-form-group-fields.name.singular'))
            ->reorderable('order')
            ->columns([
                TextColumn::make('label')
                    ->label(__('filament-satisfaction-survey-builder::filament-resources.survey-form-group-fields.fields.label')),
                TextColumn::make('order')
                    ->label(__('filament-satisfaction-survey-builder::filament-resources.survey-form-group-fields.fields.order'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('type')
                    ->label(__('filament-satisfaction-survey-builder::filament-resources.survey-form-group-fields.fields.type'))
                    ->formatStateUsing(function ($record) {
                        return $record->type->fieldName();
                    }),
                IconColumn::make('required')
                    ->label(__('filament-satisfaction-survey-builder::filament-resources.survey-form-group-fields.fields.required'))
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        return (bool)$record->required;
                    })
                    ->boolean(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('filament-satisfaction-survey-builder::filament-resources.survey-form-group-fields.create'))
                    ->visible(function () use ($form) {
                        return !$form->locked;
                    }),
                Action::make('lock_fields')
                    ->label(__('filament-satisfaction-survey-builder::filament-resources.survey-form-group-fields.lock'))
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
                    ->label(__('filament-satisfaction-survey-builder::filament-resources.survey-form-group-fields.unlock'))
                    ->requiresConfirmation()
                    ->modalHeading('Unlock Form Fields. Changing fields after entries has been made can cause inconsistencies for prexisting entries')
                    ->visible(function () use ($form) {
                        return $form->locked;
                    })
                    ->action(function () use ($form) {
                        $form->update([
                            'locked' => false,
                        ]);
                    }),
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
