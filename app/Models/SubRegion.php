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
 * Region area
 *
 * @property-read Area $parent
 * @property-read \Illuminate\Database\Eloquent\Collection $children
 * @method static Builder applyLevel(int $level)
 * @method static Builder applyParent(Area $parent)
 * @method static Builder residentRegionOnly()
 */
class SubRegion extends Area
{
    protected static function booted(): void
    {
        static::creating(function (Area $model) {
            $model->type = AreaType::SubRegion;
            $model->level = ($model->parent?->level ?? 0) + 1;
        });

        static::addGlobalScope(fn (Builder $builder) => $builder->applyType(AreaType::SubRegion));
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class, 'parent_id', 'id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(static::class, 'parent_id', 'id');
    }

    public function scopeApplyLevel(Builder $builder, int $level): void
    {
        $builder->where($this->qualifyColumn('level'), $level);
    }

    public function scopeApplyParent(Builder $builder, int|self $parent): void
    {
        if ($parent instanceof self) {
            $builder->whereBelongsTo($parent, 'parent');
        } else {
            $builder->where($this->qualifyColumn('parent_id'), $parent);
        }
    }

    public function scopeResidentRegionOnly(Builder $builder): void
    {
        $builder->applyType(AreaType::SubRegion)->applyLevel(1);
    }
}
