<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Occupant extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['building_id', 'is_resident', 'moved_in_at', 'moved_out_at'];

    public function person() : BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id', 'id');
    }

    public function building() : BelongsTo
    {
        return $this->belongsTo(Property::class, 'building_id', 'id')->buildingOnly();
    }
}
