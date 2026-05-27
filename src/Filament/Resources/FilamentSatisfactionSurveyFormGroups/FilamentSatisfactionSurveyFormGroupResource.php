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
use Luca\FilamentSatisfactionSurveyBuilder\Filament\Resources\FilamentSatisfactionSurveyFormResource\FilamentSatisfactionSurveyFormResource;
use Luca\FilamentSatisfactionSurveyBuilder\Models\SurveyFormGroup;

class FilamentSatisfactionSurveyFormGroupResource extends Resource
{
    protected static ?string $model = SurveyFormGroup::class;

    protected static ?string $slug = 'filament-satisfaction-survey-form-groups';

    protected static bool $shouldRegisterNavigation = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getIndexUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null, bool $shouldGuessMissingParameters = false): string
    {
        // On récupère le record (le groupe de questions) depuis les paramètres de la route actuelle
        $record = request()->route()->parameter('record');

        // Si on a un ID ou un modèle, on charge la relation
        if ($record) {
            if (!$record instanceof Model) {
                $record = SurveyFormGroup::find($record);
            }

            if ($record && $record->survey_form_id) {
                return FilamentSatisfactionSurveyFormResource::getUrl('edit', [
                    'record' => $record->survey_form_id
                ]);
            }
        }

        // Solution de repli si aucun record n'est trouvé (Ex: retour à la liste globale des formulaires)
        return FilamentSatisfactionSurveyFormResource::getUrl('index');
    }

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
