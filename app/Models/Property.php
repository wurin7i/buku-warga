<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['label', 'has_building'];

    protected $casts = [
        'has_building' => 'boolean',
    ];

    public function administrativeArea(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'administrative_area_id', 'id');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_id', 'id');
    }
}
