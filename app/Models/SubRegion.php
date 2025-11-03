<?php

namespace App\Models;

use App\Enums\AreaType;
use App\Enums\SubRegionLevel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use WuriN7i\IdRefs\Models\Region;

/**
 * Region area
 *
 * @property-read Area $parent
 * @property SubRegionLevel $level
 * @property-read Collection $children
 *
 * @method static Builder applyLevel(int $level)
 * @method static Builder rwOnly()
 * @method static Builder rtOnly()
 * @method static Builder applyParent(Area $parent)
 * @method static Builder subRegionOnly()
 */
class SubRegion extends Area
{
    protected $casts = [
        'level' => SubRegionLevel::class,
    ];

    protected $fillable = [
        'name',
        'level',
        'parent_id',
    ];

    protected static function booted(): void
    {
        static::creating(function (SubRegion $model) {
            $model->type = AreaType::SubRegion;

            if ($model->parent_id) {
                // Ambil parent dari database untuk mendapatkan level
                $parent = static::withoutGlobalScopes()->find($model->parent_id);
                $model->level = $parent ? $parent->level->value + 1 : SubRegionLevel::VILLAGE->value;
            } else {
                $model->level = SubRegionLevel::VILLAGE->value;
            }
        });

        // todo: apply filtering by logged in user
        static::addGlobalScope('sub_region_type', function (Builder $builder) {
            $builder->where($builder->getModel()->qualifyColumn('type'), AreaType::SubRegion->value);
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class, 'parent_id', 'id')
            ->withoutGlobalScopes();
    }

    public function children(): HasMany
    {
        return $this->hasMany(static::class, 'parent_id', 'id');
    }

    public function scopeVillageOnly(Builder $builder): void
    {
        $this->scopeApplyLevel($builder, SubRegionLevel::VILLAGE);
    }

    public function scopeRwOnly(Builder $builder): void
    {
        $this->scopeApplyLevel($builder, SubRegionLevel::RW);
    }

    public function scopeRtOnly(Builder $builder): void
    {
        $this->scopeApplyLevel($builder, SubRegionLevel::RT);
    }

    public function scopeApplyLevel(Builder $builder, SubRegionLevel $level): void
    {
        $builder->where($this->qualifyColumn('level'), $level->value);
    }

    public function scopeApplyParent(Builder $builder, int|self $parent): void
    {
        if ($parent instanceof self) {
            $builder->whereBelongsTo($parent, 'parent');
        } else {
            $builder->where($this->qualifyColumn('parent_id'), $parent);
        }
    }

    public function getParentNameAttribute(): ?string
    {
        if ($this->relationLoaded('parent') && $this->parent) {
            return $this->parent->name;
        }

        return $this->parent()->withoutGlobalScopes()->value('name');
    }
}
