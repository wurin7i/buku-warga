<?php

namespace App\Models;

use App\Enums\AreaType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use WuriN7i\IdRefs\Models\Region;

/**
 * @property string $name
 * @property Type $type
 * @property Level $level
 * @method static Builder applyType(AreaType $type)
 */
class Area extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'areas';

    protected $fillable = ['name', 'type'];

    protected $casts = [
        'type' => AreaType::class,
    ];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_id', 'id');
    }

    public function holder(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'organization_id', 'id');
    }

    public function scopeApplyType(Builder $builder, AreaType $type): void
    {
        $builder->where($this->qualifyColumn('type'), $type->value);
    }
}
