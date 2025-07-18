<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Occupant extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['building_id', 'is_resident', 'moved_in_date', 'moved_out_date'];

    protected $casts = [
        'moved_in_date' => 'date',
        'moved_out_date' => 'date',
    ];

    public function getHasMovedOutAttribute(): bool
    {
        return $this->moved_out_date && $this->moved_out_date->lt(now());
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id', 'id');
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'building_id', 'id')->buildingOnly();
    }

    public function scopeOccupyingOnly(Builder $builder): Builder
    {
        $column = $this->qualifyColumn('moved_out_date');

        return $builder->where(
            fn () => $builder->whereNull($column)
                ->orWhere($column, '>=', now())
        );
    }

    public function moveOut(?DateTime $moveOutDate = null, bool $persist = true): self
    {
        $this->moved_out_date = $moveOutDate ?? now();

        if ($persist) {
            $this->save();
        }

        return $this;
    }
}
