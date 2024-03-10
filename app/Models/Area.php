<?php

namespace App\Models;

use App\Enums\AreaType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $code
 * @property string $label
 * @property Type $type
 * @property Level $level
 * @method static Builder applyType(AreaType $type)
 */
class Area extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'areas';

    protected $fillable = ['code', 'label', 'type'];

    protected $casts = [
        'type' => AreaType::class,
    ];

    public function scopeApplyType(Builder $builder, AreaType $type): void
    {
        $builder->where($this->qualifyColumn('type'), $type->value);
    }
}
