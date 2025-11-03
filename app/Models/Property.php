<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static applySubRegion(SubRegion|int $subRegion)
 */
class Property extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['label', 'has_building', 'sub_region_id'];

    protected $casts = [
        'has_building' => 'boolean',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'owner_id', 'id');
    }

    public function occupants(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'occupants', 'building_id', 'person_id')
            ->whereRaw('(`occupants`.`moved_out_date` IS NULL OR `occupants`.`moved_out_date` >= NOW())')
            ->withPivot(['is_resident', 'moved_in_date', 'moved_out_date']);
    }

    public function subRegion(): BelongsTo
    {
        return $this->belongsTo(SubRegion::class, 'sub_region_id', 'id')
            ->rtOnly();
    }

    public function cluster(): BelongsTo
    {
        return $this->belongsTo(Cluster::class, 'cluster_id', 'id');
    }

    public function scopeBuildingOnly(Builder $builder, bool $bool = true): Builder
    {
        return $builder->where($this->qualifyColumn('has_building'), $bool);
    }

    public function scopeApplySubRegion(Builder $builder, SubRegion|int $subRegion): void
    {
        $builder->where($this->qualifyColumn('sub_region_id'), $subRegion);
    }
}
