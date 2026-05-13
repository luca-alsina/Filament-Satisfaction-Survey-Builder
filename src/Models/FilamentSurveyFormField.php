<?php

namespace Luca\FilamentSatisfactionSurveyBuilder\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Luca\FilamentSatisfactionSurveyBuilder\Enums\FilamentFieldTypeEnum;
use Luca\FilamentSatisfactionSurveyBuilder\Models\Traits\BelongsToTenant;

/**
 * @property int $id
 * @property string $label
 * @property FilamentFieldTypeEnum $type
 * @property array|null $options
 * @property array|null $rules
 * @property int $order
 * @property-read FilamentSurveyForm $filamentForm
 */
class FilamentSurveyFormField extends Model implements Sortable
{
    use BelongsToTenant;
    use HasFactory;
    use SortableTrait;

    public $sortable = [
        'order_column_name' => 'order',
        'sort_when_creating' => true,
    ];

    protected $guarded = [];

    protected $casts = [
        'type' => FilamentFieldTypeEnum::class,
        'options' => 'array',
        'rules' => 'array',
        'schema' => 'array',
    ];

    public function filamentForm(): BelongsTo
    {
        return $this->belongsTo(FilamentSurveyForm::class);
    }
}
