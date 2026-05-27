<?php

namespace Luca\FilamentSatisfactionSurveyBuilder\Filament\Resources\FilamentSatisfactionSurveyFormResource\RelationManagers;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Luca\FilamentSatisfactionSurveyBuilder\Exports\FilamentFormUsersExport;
use Maatwebsite\Excel\Facades\Excel;

class FilamentSatisfactionSurveyFormUsersRelationManager extends RelationManager
{
    protected static string $relationship = 'filamentFormUsers';

    /**
     * Apps may restrict visibility of the Entries relation manager per form by defining
     * a viewEntries($user, $form) method on the FilamentForm (owner) policy. When present,
     * that policy is used; otherwise the default (related model viewAny) applies.
     */
    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        if (!parent::canViewForRecord($ownerRecord, $pageClass)) {
            return false;
        }

        $user = Auth::user();
        if (!$user) {
            return false;
        }

        return self::userCanViewEntriesForOwner($user, $ownerRecord);
    }

    /**
     * Whether the given user can view/export entries for the given owner form.
     * Uses the owner model's viewEntries policy when present.
     */
    protected static function userCanViewEntriesForOwner(Authenticatable&Authorizable $user, Model $owner): bool
    {
        $policy = policy($owner);
        if ($policy && method_exists($policy, 'viewEntries')) {
            return $user->can('viewEntries', $owner);
        }

        return true;
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __(config('filament-satisfaction-survey-builder.admin-panel-filament-form-user-name-plural'));
    }

    public static function getLabel(): string
    {
        return 'Custom Posts Title';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label(__('User'))
                    ->columnSpanFull()
                    ->options(function () {
                        return config('auth.providers.users.model', \Illuminate\Foundation\Auth\User::class)::all()->pluck(config('filament-satisfaction-survey-builder.user_title_attribute', 'name'), 'id')->toArray();
                    })
                    ->searchable()
                    ->required()
                    ->disabledOn('edit'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user.' . config('filament-satisfaction-survey-builder.user_title_attribute', 'name'))
            ->heading(config('filament-satisfaction-survey-builder.admin-panel-filament-form-user-name-plural'))
            ->columns([
                TextColumn::make('user.' . config('filament-satisfaction-survey-builder.user_title_attribute', 'name'))
                    ->sortable()
                    ->searchable(),

                IconColumn::make('response_exists')
                    ->label('Response Exists')
                    ->sortable()
                    ->boolean()
                    ->getStateUsing(fn($record) => $record->entry !== null),

                TextColumn::make('created_at')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->sortable(),
            ])
            ->recordUrl(fn($record) => route(config('filament-satisfaction-survey-builder.filament-form-user-show-route'), $record))
            ->filters([
                Filter::make('guest_entries')
                    ->query(fn(Builder $query): Builder => $query->whereNull('user_id')),
                Filter::make('user_entries')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('user_id')),
            ])
            ->headerActions([
                CreateAction::make('create')
                    ->label(__('Add user'))
            ])
            ->recordActions([
                ActionGroup::make([
                    DeleteAction::make(),
                ]),
            ], position: RecordActionsPosition::BeforeColumns)
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('Export Selected')
                        ->action(fn(Collection $records) => Excel::download(
                            new FilamentFormUsersExport($records),
                            urlencode($this->getOwnerRecord()->name) . '_form_entry_export' . now()->format('Y-m-dhis') . '.csv')
                        )
                        ->icon('heroicon-o-document-chart-bar')
                        ->deselectRecordsAfterCompletion()
                        ->visible(fn(): bool => $this->canViewEntriesForOwner()),
                ]),
            ]);
    }

    /**
     * Whether the current user can view/export entries for the owner form.
     * Uses the owner model's viewEntries policy when present.
     */
    protected function canViewEntriesForOwner(): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        /** @var Authenticatable&Authorizable $user */
        return self::userCanViewEntriesForOwner($user, $this->getOwnerRecord());
    }
}
