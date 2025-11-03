<?php

namespace App\Models;

use App\Enums\AreaType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Cluster area
 */
class Cluster extends Area
{
    protected static function booted(): void
    {
        static::creating(function (Area $model) {
            $model->type = AreaType::Cluster;
        });

        static::addGlobalScope(fn (Builder $builder) => $builder->applyType(AreaType::Cluster));
    }

    public function baseArea(): BelongsTo
    {
        return $this->belongsTo(SubRegion::class, 'parent_id', 'id');
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'cluster_id');
    }
}
