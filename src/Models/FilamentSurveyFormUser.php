<?php

namespace Luca\FilamentSatisfactionSurveyBuilder\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Luca\FilamentSatisfactionSurveyBuilder\Models\Traits\BelongsToTenant;

/**
 * @property array $entry
 * @property array|null $firstEntry
 * @property int|null $user_id
 * @property-read array $key_value_entry
 * @property-read FilamentSurveyForm $filamentForm
 */
class FilamentSurveyFormUser extends Model implements HasMedia
{
    use BelongsToTenant;
    use HasFactory;
    use InteractsWithMedia;

    protected $table = 'filament_form_user';

    protected $guarded = [];

    protected $casts = [
        'entry' => 'json',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model', Authenticatable::class));
    }

    public function filamentForm(): BelongsTo
    {
        return $this->belongsTo(FilamentSurveyForm::class);
    }

    public function getKeyValueEntryAttribute()
    {
        $keyValueEntry = [];

        foreach ($this->entry as $fieldEntry) {
            if (is_array($fieldEntry['answer'])) {
                $keyValueEntry[$fieldEntry['field']] = json_encode($fieldEntry['answer']);
            } else {
                $keyValueEntry[$fieldEntry['field']] = $fieldEntry['answer'];
            }
        }

        return $keyValueEntry;
    }
}
