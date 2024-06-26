<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use WuriN7i\IdRefs\Models\Citizenship;
use WuriN7i\IdRefs\Models\BloodType;
use WuriN7i\IdRefs\Models\Gender;
use WuriN7i\IdRefs\Models\Marital;
use WuriN7i\IdRefs\Models\Occupation;
use WuriN7i\IdRefs\Models\Religion;

class Person extends Model
{
    use HasFactory;

    protected $fillable = ['nik', 'kk_number', 'name', 'gender_id', 'birth_date', 'birth_place'];

    public function occupy(): HasOne
    {
        return $this->hasOne(Occupant::class, 'person_id', 'id')
            ->occupyingOnly()
            ->latestOfMany();
    }

    public function gender(): BelongsTo
    {
        return $this->belongsTo(Gender::class, 'gender_id', 'id');
    }

    public function bloodType(): BelongsTo
    {
        return $this->belongsTo(BloodType::class, 'blood_type_id', 'id');
    }

    public function religion(): BelongsTo
    {
        return $this->belongsTo(Religion::class, 'religion_id', 'id');
    }

    public function occupation(): BelongsTo
    {
        return $this->belongsTo(Occupation::class, 'occupation_id', 'id');
    }

    public function marital(): BelongsTo
    {
        return $this->belongsTo(Marital::class, 'marital_id', 'id');
    }

    public function citizenship(): BelongsTo
    {
        return $this->belongsTo(Citizenship::class, 'citizenship_id', 'id');
    }

    public function getIsOccupyingAttribute(): bool
    {
        $occupy = $this->occupy;

        return $occupy && !$occupy->has_moved_out;
    }
}
