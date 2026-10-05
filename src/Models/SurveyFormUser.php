<?php

namespace Luca\FilamentSatisfactionSurveyBuilder\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Luca\FilamentSatisfactionSurveyBuilder\Models\Traits\BelongsToTenant;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property array $entry
 * @property array|null $firstEntry
 * @property int|null $user_id
 * @property string|null $token
 * @property-read array $key_value_entry
 * @property-read string $entry_link_with_token
 * @property-read SurveyForm $filamentForm
 */
class SurveyFormUser extends Model implements HasMedia
{
    use BelongsToTenant;
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'survey_form_id',
        'user_id',
        'registration_id',
        'entry',
        'token',
    ];

    protected $table = 'survey_form_users';

    protected $guarded = [];

    protected $casts = [
        'entry' => 'json',
    ];

    protected $dispatchesEvents = [
        'creating' => \Luca\FilamentSatisfactionSurveyBuilder\Events\SurveyFormUserCreating::class,
        'updated' => \App\Domain\Mail\Models\Events\SurveyFormUserUpdated::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model', Authenticatable::class));
    }

    public function filamentForm(): BelongsTo
    {
        return $this->belongsTo(SurveyForm::configuredClass(), 'survey_form_id');
    }

    public function getFormLinkWithTokenAttribute(): string
    {
        return route('filament-satisfaction-survey-builder.show.token', [
            'form' => $this->survey_form_id,
            'token' => $this->token,
        ]);
    }

    /**
     * Whether the given token identifies this submission.
     *
     * The token is the bearer credential sent by email (or built through
     * `form_link_with_token`): whoever holds it is considered to be the
     * person the submission belongs to, authenticated or not.
     */
    public function hasValidToken(?string $token): bool
    {
        if (blank($token) || blank($this->token)) {
            return false;
        }

        return hash_equals((string) $this->token, (string) $token);
    }

    /**
     * URL of the entry page carrying the identification token, so the
     * recipient can be authorized without a session.
     */
    public function getEntryLinkWithTokenAttribute(): string
    {
        return route(
            config('filament-satisfaction-survey-builder.filament-form-user-show-route'),
            ['entry' => $this->getKey(), 'token' => $this->token],
        );
    }

    public function getKeyValueEntryAttribute()
    {
        $keyValueEntry = [];

        if (is_array($this->entry)) {
            foreach ($this->entry as $fieldEntry) {
                if (is_array($fieldEntry['answer'])) {
                    $keyValueEntry[$fieldEntry['field']] = json_encode($fieldEntry['answer']);
                } else {
                    $keyValueEntry[$fieldEntry['field']] = $fieldEntry['answer'];
                }
            }
        }

        return $keyValueEntry;
    }
}
