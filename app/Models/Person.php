<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use WuriN7i\IdRefs\Models\BloodType;
use WuriN7i\IdRefs\Models\Citizenship;
use WuriN7i\IdRefs\Models\Gender;
use WuriN7i\IdRefs\Models\Marital;
use WuriN7i\IdRefs\Models\Occupation;
use WuriN7i\IdRefs\Models\Region;
use WuriN7i\IdRefs\Models\Religion;

/**
 * @property Occupant $occupy
 *
 * @method static applyIsResident(bool $occupying = true)
 * @method static applyIsDeceased(bool $deceased = true)
 * @method static applyIsAlive(bool $alive = true)
 */
class Person extends Model
{
    use HasFactory;

    protected $fillable = [
        'nik',
        'kk_number',
        'name',
        'birth_date',
        'birth_place',
        'is_deceased',
        'address',
        'sub_region',
        'notes',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        //
    }

    public function residents(): BelongsToMany
    {
        return $this->belongsToMany(Property::class, 'occupants', 'person_id', 'building_id')
            ->withPivot(['is_resident', 'moved_in_date', 'moved_out_date']);
    }

    public function occupy(): HasOne
    {
        return $this->hasOne(Occupant::class, 'person_id')
            ->ofMany(
                ['id' => 'MAX'],
                fn (Builder $q) => $q->occupyingOnly()
            );
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

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_id', 'id');
    }

    public function scopeApplyIsDeceased(Builder $builder, bool $value = true): void
    {
        $builder->where($this->qualifyColumn('is_deceased'), $value);
    }

    public function scopeApplyIsAlive(Builder $builder, bool $value = true): void
    {
        $this->scopeApplyIsDeceased($builder, ! $value);
    }

    public function scopeApplyIsResident(Builder $builder, bool $value = true): void
    {
        $value ? $builder->has('occupy') : $builder->doesntHave('occupy');
    }

    public function getIsOccupyingAttribute(): bool
    {
        $occupy = $this->occupy;

        return $occupy && ! $occupy->has_moved_out;
    }

    public function declareDeath(?DateTime $deathDate = null, bool $persist = true): self
    {
        $this->is_deceased = true;

        if ($persist) {
            $this->save();
        }

        return $this;
    }
}
