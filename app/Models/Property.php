<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    public function sub_region(): BelongsTo
    {
        return $this->belongsTo(SubRegion::class, 'sub_region_id', 'id');
    }

    public function cluster(): BelongsTo
    {
        return $this->belongsTo(Cluster::class, 'cluster_id', 'id');
    }

    public function scopeBuildingOnly(Builder $builder, bool $bool = true) : Builder
    {
        return $builder->where($this->qualifyColumn('has_building'), $bool);
    }
}
