<?php

namespace Luca\FilamentSatisfactionSurveyBuilder\Filament\Resources\FilamentSatisfactionSurveyFormGroups;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Luca\FilamentSatisfactionSurveyBuilder\Filament\Resources\FilamentSatisfactionSurveyFormGroups\RelationManagers\FilamentSatisfactionSurveyFormGroupFieldsRelationManager;
use Luca\FilamentSatisfactionSurveyBuilder\Filament\Resources\FilamentSatisfactionSurveyFormGroups\Schemas\FilamentSatisfactionSurveyFormGroupForm;
use Luca\FilamentSatisfactionSurveyBuilder\Filament\Resources\FilamentSatisfactionSurveyFormGroups\Schemas\FilamentSatisfactionSurveyFormGroupInfolist;
use Luca\FilamentSatisfactionSurveyBuilder\Filament\Resources\FilamentSatisfactionSurveyFormGroups\Tables\FilamentSatisfactionSurveyFormGroupsTable;
use Luca\FilamentSatisfactionSurveyBuilder\Models\SurveyFormGroup;

class FilamentSatisfactionSurveyFormGroupResource extends Resource
{
    protected static ?string $model = SurveyFormGroup::class;

    protected static ?string $slug = 'filament-satisfaction-survey-form-groups';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return FilamentSatisfactionSurveyFormGroupForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return FilamentSatisfactionSurveyFormGroupInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FilamentSatisfactionSurveyFormGroupsTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            /*            'index' => Pages\ListFilamentSatisfactionSurveyFormGroups::route('/'),
                        'create' => Pages\CreateFilamentSatisfactionSurveyFormGroup::route('/create'),*/
            'edit' => Pages\EditFilamentSatisfactionSurveyFormGroup::route('/{record}/edit'),
        ];
    }

    /**
     * @return Builder<SurveyFormGroup>
     */
    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['filamentForm']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'filamentForm.name'];
    }

    public static function getRelations(): array
    {
        return [
            FilamentSatisfactionSurveyFormGroupFieldsRelationManager::class,
        ];
    }

    /**
     * @param SurveyFormGroup $record
     */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [];

        if ($record->filamentForm) {
            $details['FilamentForm'] = $record->filamentForm->name;
        }

        return $details;
    }
}
