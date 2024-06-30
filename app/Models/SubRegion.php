<?php

namespace App\Models;

use App\Enums\AreaType;
use App\Enums\SubRegionLevel;
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
 * @property SubRegionLevel $level
 * @property-read \Illuminate\Database\Eloquent\Collection $children
 * @method static Builder applyLevel(int $level)
 * @method static Builder rwOnly()
 * @method static Builder rtOnly()
 * @method static Builder applyParent(Area $parent)
 * @method static Builder subRegionOnly()
 */
class SubRegion extends Area
{
    protected $casts = [
        // TODO: apply cast & refactor app/Filament/Resources/ClusterResource/RelationManagers/PropertiesRelationManager.php:34
        // 'level' => SubRegionLevel::class,
    ];

    protected static function booted(): void
    {
        static::creating(function (Area $model) {
            $model->type = AreaType::SubRegion;
        });

        static::updating(function ($model) {
            $model->level = ($model->parent?->level ?? SubRegionLevel::VILLAGE->value) + 1;
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
}
