<?php

namespace Luca\FilamentSatisfactionSurveyBuilder\Filament\Resources\FilamentSatisfactionSurveyFormResource;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Luca\FilamentSatisfactionSurveyBuilder\Filament\Resources\FilamentSatisfactionSurveyFormResource\Pages\CreateFilamentForm;
use Luca\FilamentSatisfactionSurveyBuilder\Filament\Resources\FilamentSatisfactionSurveyFormResource\Pages\EditFilamentForm;
use Luca\FilamentSatisfactionSurveyBuilder\Filament\Resources\FilamentSatisfactionSurveyFormResource\Pages\ListFilamentForms;
use Luca\FilamentSatisfactionSurveyBuilder\Filament\Resources\FilamentSatisfactionSurveyFormResource\RelationManagers\FilamentSatisfactionSurveyFormGroupsRelationManager;
use Luca\FilamentSatisfactionSurveyBuilder\Filament\Resources\FilamentSatisfactionSurveyFormResource\RelationManagers\FilamentSatisfactionSurveyFormUsersRelationManager;
use Luca\FilamentSatisfactionSurveyBuilder\Models\SurveyForm;
use Luca\FilamentSatisfactionSurveyBuilder\Models\SurveyFormGroupField;

class FilamentSatisfactionSurveyFormResource extends Resource
{
    protected static ?string $model = SurveyForm::class;

    protected static ?int $navigationSort = 99;

    /**
     * Check if this resource should be scoped to a tenant.
     */
    public static function isScopedToTenant(): bool
    {
        return config('filament-satisfaction-survey-builder.tenancy.enabled', false);
    }

    /**
     * Get the tenant ownership relationship name.
     */
    public static function getTenantOwnershipRelationshipName(): string
    {
        if (!config('filament-satisfaction-survey-builder.tenancy.enabled')) {
            return 'tenant';
        }

        return SurveyForm::getTenantRelationshipName();
    }

    public static function getBreadcrumb(): string
    {
        return __('filament-satisfaction-survey-builder::filament-resources.survey-form-group-fields.name.plural');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament-satisfaction-survey-builder::filament-resources.survey-form-group-fields.name.plural');
    }

    public static function getNavigationIcon(): ?string
    {
        return config('filament-satisfaction-survey-builder.admin-panel-icon');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-satisfaction-survey-builder::filament-resources.survey-form.name.plural');
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-satisfaction-survey-builder.admin-panel-sort-order');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('filament-satisfaction-survey-builder::filament-resources.survey-form.fields.name'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('redirect_url')
                    ->label(__('filament-satisfaction-survey-builder::filament-resources.survey-form.fields.redirect_url'))
                    ->hint(__('filament-satisfaction-survey-builder::filament-resources.survey-form.fields.redirect_url_hint')),
                RichEditor::make('description')
                    ->label(__('filament-satisfaction-survey-builder::filament-resources.survey-form.fields.description'))
                    ->columnSpanFull(),
                Section::make(__('filament-satisfaction-survey-builder::filament-resources.survey-form.sections.template'))
                    ->description(__('filament-satisfaction-survey-builder::filament-resources.survey-form.sections.template_description'))
                    ->schema([
                        Toggle::make('is_template')
                            ->label(__('filament-satisfaction-survey-builder::filament-resources.survey-form.fields.is_template'))
                            ->hint(__('filament-satisfaction-survey-builder::filament-resources.survey-form.fields.is_template_hint'))
                            ->live()
                            ->disabled(fn(?SurveyForm $record): bool => $record && $record->filamentFormUsers()->exists())
                            ->dehydrateStateUsing(function ($state, ?SurveyForm $record): bool {
                                if ($state && $record && $record->users()->exists()) {
                                    return false;
                                }
                                return (bool)$state;
                            })
                            ->afterStateUpdated(function ($state, Set $set) {
                                if ($state) {
                                    $set('template_id', null);
                                }
                            }),
                        Select::make('template_id')
                            ->label(__('filament-satisfaction-survey-builder::filament-resources.survey-form.fields.template_id'))
                            ->helperText(__('filament-satisfaction-survey-builder::filament-resources.survey-form.fields.template_id_helper'))
                            ->relationship('template', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn(Get $get): bool => !(bool)$get('is_template'))
                            ->hidden(fn(Get $get): bool => (bool)$get('is_template'))
                            ->afterStateUpdated(function ($state, Set $set) {
                                if ($state) {
                                    $set('is_template', false);
                                }
                            }),
                    ])
                    ->collapsible()
                    ->columnSpanFull()
                    ->collapsed(),
                Section::make(__('filament-satisfaction-survey-builder::filament-resources.survey-form.sections.notifications'))
                    ->description(__('filament-satisfaction-survey-builder::filament-resources.survey-form.sections.notifications_description'))
                    ->schema([
                        static::getNotificationEmailsField(),
                    ])
                    ->collapsible()
                    ->collapsed(),
                Section::make(__('filament-satisfaction-survey-builder::filament-resources.survey-form.sections.limitations'))
                    ->description(__('filament-satisfaction-survey-builder::filament-resources.survey-form.sections.limitations_description'))
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Toggle::make('restricted_to_users')
                            ->label(__('filament-satisfaction-survey-builder::filament-resources.survey-form.fields.restricted_to_users'))
                            ->hint(__('filament-satisfaction-survey-builder::filament-resources.survey-form.fields.restricted_to_users_hint'))
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set) {
                                if ($state) {
                                    $set('private_entries', false);
                                    $set('permit_guest_entries', false);
                                }
                            }),
                        Toggle::make('private_entries')
                            ->label(__('filament-satisfaction-survey-builder::filament-resources.survey-form.fields.private_entries'))
                            ->hint(__('filament-satisfaction-survey-builder::filament-resources.survey-form.fields.private_entries_hint'))
                            ->disabled(fn(?SurveyForm $record): bool => static::userCannotChangePrivateEntries($record))
                            ->dehydrateStateUsing(fn($state, ?SurveyForm $record): bool => static::userCannotChangePrivateEntries($record) && $record
                                ? (bool)$record->private_entries
                                : (bool)$state)
                            ->hidden(fn(Get $get): bool => (bool)$get('restricted_to_users')),
                        Toggle::make('permit_guest_entries')
                            ->label(__('filament-satisfaction-survey-builder::filament-resources.survey-form.fields.permit_guest_entries'))
                            ->hint(__('filament-satisfaction-survey-builder::filament-resources.survey-form.fields.permit_guest_entries_hint'))
                            ->hidden(fn(Get $get): bool => (bool)$get('restricted_to_users')),
                    ]),
                Section::make(__('filament-satisfaction-survey-builder::filament-resources.survey-form.sections.average_data'))
                    ->description(__('filament-satisfaction-survey-builder::filament-resources.survey-form.sections.average_data_description'))
                    ->visible(fn(?SurveyForm $record, string $operation): bool => $operation === 'edit' && (bool)$record && $record->average_data)
                    ->columnSpanFull()
                    ->collapsible()
                    ->collapsed(false)
                    ->schema([
                        View::make('filament-satisfaction-survey-builder::filament.resources.survey-form.average-data')
                            ->columnSpanFull()
                            ->viewData(fn(?SurveyForm $record): array => [
                                'averageDataRows' => static::getAverageDataRows($record),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label(__('filament-satisfaction-survey-builder::filament-resources.survey-form.table.columns.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('filament-satisfaction-survey-builder::filament-resources.survey-form.table.columns.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')
                    ->label(__('filament-satisfaction-survey-builder::filament-resources.survey-form.table.columns.name'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('form_link')
                    ->label(__('filament-satisfaction-survey-builder::filament-resources.survey-form.table.columns.form_link'))
                    ->copyable()
                    ->copyMessage(__('filament-satisfaction-survey-builder::filament-resources.survey-form.table.copy_message'))
                    ->copyMessageDuration(1500),
                IconColumn::make('permit_guest_entries')
                    ->label(__('filament-satisfaction-survey-builder::filament-resources.survey-form.table.columns.permit_guest_entries'))
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        return (bool)$record->permit_guest_entries;
                    })
                    ->boolean(),
                IconColumn::make('private_entries')
                    ->label(__('filament-satisfaction-survey-builder::filament-resources.survey-form.table.columns.private_entries'))
                    ->sortable()
                    ->boolean(),
                IconColumn::make('locked')
                    ->label(__('filament-satisfaction-survey-builder::filament-resources.survey-form.table.columns.locked'))
                    ->sortable()
                    ->boolean(),
                IconColumn::make('is_template')
                    ->label(__('filament-satisfaction-survey-builder::filament-resources.survey-form.table.columns.is_template'))
                    ->sortable()
                    ->boolean(),
                TextColumn::make('template.name')
                    ->label(__('filament-satisfaction-survey-builder::filament-resources.survey-form.table.columns.template'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('is_template')
                    ->label(__('filament-satisfaction-survey-builder::filament-resources.survey-form.filters.is_template'))
                    ->options([
                        '1' => __('filament-satisfaction-survey-builder::filament-resources.survey-form.filters.templates'),
                        '0' => __('filament-satisfaction-survey-builder::filament-resources.survey-form.filters.forms'),
                    ]),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    Action::make('preview')
                        ->label(__('filament-satisfaction-survey-builder::filament-resources.survey-form.actions.preview'))
                        ->visible(fn() => (bool)config('filament-satisfaction-survey-builder.preview-route'))
                        ->url(fn($record) => route(config('filament-satisfaction-survey-builder.preview-route'), ['form' => $record->id]))
                        ->openUrlInNewTab(),
                    Action::make('copy')
                        ->label(__('filament-satisfaction-survey-builder::filament-resources.survey-form.actions.copy'))
                        ->visible(fn(): bool => static::canCreate())
                        ->authorize(fn(): bool => static::canCreate())
                        ->action(function ($record) {
                            $formCopy = SurveyForm::create([
                                'name' => $record->name . ' - (Copy)',
                                'permit_guest_entries' => $record->permit_guest_entries,
                                'private_entries' => $record->private_entries,
                                'redirect_url' => $record->redirect_url,
                                'description' => $record->description,
                                'notification_emails' => $record->notification_emails,
                            ]);

                            $record->filamentFormFields->each(function ($field) use ($formCopy) {
                                SurveyFormGroupField::create([
                                    'filament_form_id' => $formCopy->id,
                                    'label' => $field->label,
                                    'type' => $field->type,
                                    'required' => $field->required,
                                    'order' => $field->order,
                                    'hint' => $field->hint,
                                    'options' => $field->options,
                                    'rules' => $field->rules,
                                ]);
                            });

                            Notification::make()
                                ->title(__('filament-satisfaction-survey-builder::filament-resources.survey-form.actions.copy_success_title'))
                                ->body(__('filament-satisfaction-survey-builder::filament-resources.survey-form.actions.copy_success_body'))
                                ->success()
                                ->send();

                            return Redirect::to('/admin/filament-forms/' . $formCopy->id . '/edit');
                        }),
                ]),
            ], position: RecordActionsPosition::BeforeColumns)
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading(__('filament-satisfaction-survey-builder::filament-resources.survey-form.empty_state_heading'));
    }

    public static function getRelations(): array
    {
        return [
            FilamentSatisfactionSurveyFormGroupsRelationManager::class,
            FilamentSatisfactionSurveyFormUsersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFilamentForms::route('/'),
            'create' => CreateFilamentForm::route('/create'),
            'edit' => EditFilamentForm::route('/{record}/edit'),
        ];
    }

    /**
     * True when the current user must not be allowed to change the private_entries toggle.
     * Used when the form is private and the app's viewEntries policy denies the user.
     */
    protected static function userCannotChangePrivateEntries(?SurveyForm $record): bool
    {
        if (!$record || !$record->exists || !(bool)$record->private_entries) {
            return false;
        }

        $user = Auth::user();
        if (!$user) {
            return true;
        }

        $policy = policy($record);
        if ($policy && method_exists($policy, 'viewEntries')) {
            return !$user->can('viewEntries', $record);
        }

        return false;
    }

    protected static function getNotificationEmailsField(): Component
    {
        $userModel = config('filament-satisfaction-survey-builder.user_model');

        // If user model is configured, use Select with user search
        if ($userModel && class_exists($userModel)) {
            return Select::make('notification_emails')
                ->label(__('filament-satisfaction-survey-builder::filament-resources.survey-form.fields.notification_emails'))
                ->helperText(__('Select users who should receive notifications when this form is submitted.'))
                ->multiple()
                ->searchable()
                ->getSearchResultsUsing(function ($search) use ($userModel) {
                    return $userModel::query()
                        ->where(function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        })
                        ->limit(50)
                        ->get()
                        ->mapWithKeys(fn($user) => [$user->email => $user->name . ' (' . $user->email . ')']);
                })
                ->getOptionLabelsUsing(function (array $values) use ($userModel): array {
                    return $userModel::whereIn('email', $values)
                        ->get()
                        ->mapWithKeys(fn($user) => [$user->email => $user->name . ' (' . $user->email . ')'])
                        ->toArray();
                });
        }

        // Default: TagsInput for manual email entry
        return TagsInput::make('notification_emails')
            ->label(__('filament-satisfaction-survey-builder::filament-resources.survey-form.fields.notification_emails'))
            ->helperText(__('filament-satisfaction-survey-builder::filament-resources.survey-form.fields.notification_emails_helper'))
            ->placeholder('email@example.com');
    }

    /**
     * @return array<int, array{
     *     field_id: int|string,
     *     label: string,
     *     field_type: string|null,
     *     average_type: int|null,
     *     average: mixed
     * }>
     */
    protected static function getAverageDataRows(?SurveyForm $record): array
    {
        if (!$record || !is_array($record->average_data) || empty($record->average_data)) {
            return [];
        }

        return collect($record->average_data)
            ->map(function ($fieldData, $fieldId): array {
                $fieldData = is_array($fieldData) ? $fieldData : [];

                return [
                    'field_id' => $fieldId,
                    'label' => (string)($fieldData['label'] ?? ('#' . $fieldId)),
                    'field_type' => $fieldData['field_type'] ?? null,
                    'average_type' => isset($fieldData['average_type']) ? (int)$fieldData['average_type'] : null,
                    'average' => $fieldData['average'] ?? null,
                ];
            })
            ->values()
            ->all();
    }
}
