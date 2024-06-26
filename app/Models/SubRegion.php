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
 * @method static Builder rwOnly()
 * @method static Builder applyParent(Area $parent)
 * @method static Builder subRegionOnly()
 */
class SubRegion extends Area
{
    public const LEVEL_RW = 1;
    public const LEVEL_RT = 2;

    protected static function booted(): void
    {
        static::creating(function (Area $model) {
            $model->type = AreaType::SubRegion;
            $model->level = ($model->parent?->level ?? 0) + 1;
        });

        // todo: apply filtering by logged in user
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

    public function scopeRwOnly(Builder $builder): void
    {
        $this->scopeApplyLevel($builder, self::LEVEL_RW);
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
}
