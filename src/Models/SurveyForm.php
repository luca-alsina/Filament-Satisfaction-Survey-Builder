<?php

namespace Luca\FilamentSatisfactionSurveyBuilder\Models;

use App\Domain\Billing\Models\Registration;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Luca\FilamentSatisfactionSurveyBuilder\Models\Traits\BelongsToTenant;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $redirect_url
 * @property bool $permit_guest_entries
 * @property bool $private_entries
 * @property array<int, string>|null $notification_emails
 * @property bool $is_template
 * @property int|null $template_id
 * @property-read string $form_link
 */
class SurveyForm extends Model
{
    use BelongsToTenant;
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    protected $casts = [
        'permit_guest_entries' => 'boolean',
        'private_entries' => 'boolean',
        'is_template' => 'boolean',
        'notification_emails' => 'array',
        'average_data' => 'array'
    ];

    public function users(): HasManyThrough
    {
        $userModel = config(
            'filament-satisfaction-survey-builder.user_model',
            config('auth.providers.users.model', Authenticatable::class)
        );

        return $this->hasManyThrough(
            $userModel,
            SurveyFormUser::class,
            'survey_form_id',
            'id',
            'id',
            'user_id'
        )->distinct();
    }

    public function filamentFormUsers(): HasMany
    {
        return $this->hasMany(SurveyFormUser::class);
    }

    public function filamentFormFields(): HasManyThrough
    {
        return $this->hasManyThrough(SurveyFormGroupField::class, SurveyFormGroup::class);
    }

    public function filamentFormGroups(): HasMany
    {
        return $this->hasMany(SurveyFormGroup::class)
            ->orderBy('order', 'asc');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(SurveyForm::class, 'template_id');
    }

    public function templates(): HasMany
    {
        return $this->hasMany(SurveyForm::class, 'template_id');
    }

    public function getFormLinkAttribute(): string
    {
        return route(config('filament-satisfaction-survey-builder.filament-form-show-route'), $this->id);
    }

    public function addUser(Authenticatable|int $user, ?Registration $registration = null): void
    {
        $userId = $user instanceof Authenticatable ? $user->id : $user;
        $this->filamentFormUsers()->create(['user_id' => $userId, 'registration_id' => $registration?->id]);
    }

    public function getFormUserByToken(string $token): ?SurveyFormUser
    {
        return $this->filamentFormUsers()->where('token', $token)->first();
    }
}
