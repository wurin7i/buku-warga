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
 * Community area
 */
class CommunityArea extends Area
{
    protected static function booted(): void
    {
        static::creating(function (Area $model) {
            $model->type = AreaType::Community;
        });

        static::addGlobalScope(fn (Builder $builder) => $builder->applyType(AreaType::Community));
    }

    public function base_area() : BelongsTo
    {
        return $this->belongsTo(LocaleArea::class, 'base_area_id', 'id');
    }
}
