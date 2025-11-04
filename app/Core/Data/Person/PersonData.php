<?php

namespace App\Core\Data\Person;

use App\Models\Person;
use App\Core\Data\NIK\NIKData;
use App\Core\Data\Occupancy\OccupancyData;
use App\Core\Data\Property\PropertyData;
use App\Core\Helpers\NIKHelper;
use Carbon\Carbon;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class PersonData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $nik,
        public readonly ?string $kkNumber,
        public readonly string $name,

        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public readonly ?Carbon $birthDate,

        public readonly ?string $birthPlace,
        public readonly bool $isDeceased,
        public readonly ?string $address,
        public readonly ?string $subRegion,
        public readonly ?string $notes,

        // Relations
        public readonly ?OccupancyData $occupancy,
        public readonly ?PropertyData $currentProperty,

        #[WithCast(DateTimeInterfaceCast::class)]
        public readonly ?Carbon $createdAt,

        #[WithCast(DateTimeInterfaceCast::class)]
        public readonly ?Carbon $updatedAt,
    ) {}

    public static function fromModel(Person $person): self
    {
        return new self(
            id: $person->id,
            nik: $person->nik,
            kkNumber: $person->kk_number,
            name: $person->name,
            birthDate: $person->birth_date,
            birthPlace: $person->birth_place,
            isDeceased: $person->is_deceased,
            address: $person->address,
            subRegion: $person->sub_region,
            notes: $person->notes,
            occupancy: $person->occupy ? OccupancyData::fromModel($person->occupy) : null,
            currentProperty: $person->occupy?->building ? PropertyData::fromModel($person->occupy->building) : null,
            createdAt: $person->created_at,
            updatedAt: $person->updated_at,
        );
    }

    public function toModel(): Person
    {
        return new Person([
            'nik' => $this->nik,
            'kk_number' => $this->kkNumber,
            'name' => $this->name,
            'birth_date' => $this->birthDate,
            'birth_place' => $this->birthPlace,
            'is_deceased' => $this->isDeceased,
            'address' => $this->address,
            'sub_region' => $this->subRegion,
            'notes' => $this->notes,
        ]);
    }

    /**
     * Get age in years (from birth_date or NIK)
     */
    public function getAge(): ?int
    {
        if ($this->birthDate) {
            return $this->birthDate->age;
        }

        return NIKHelper::calculateAge($this->nik);
    }

    /**
     * Check if person has current occupancy
     */
    public function hasCurrentOccupancy(): bool
    {
        return $this->occupancy !== null && $this->occupancy->isCurrent;
    }

    /**
     * Get full address including property and sub-region
     */
    public function getFullAddress(): string
    {
        $parts = array_filter([
            $this->address,
            $this->currentProperty?->label,
            $this->currentProperty?->subRegionName,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Get gender from NIK
     */
    public function getGender(): ?\WuriN7i\IdRefs\Enums\Gender
    {
        return NIKHelper::extractGender($this->nik);
    }

    /**
     * Check if female
     */
    public function isFemale(): bool
    {
        return NIKHelper::isFemale($this->nik);
    }

    /**
     * Check if male
     */
    public function isMale(): bool
    {
        return NIKHelper::isMale($this->nik);
    }

    /**
     * Get birth date from NIK if not available from data
     */
    public function getBirthDateFromNIK(): ?Carbon
    {
        return NIKHelper::extractBirthDate($this->nik);
    }

    /**
     * Get effective birth date (prefer stored data, fallback to NIK)
     */
    public function getEffectiveBirthDate(): ?Carbon
    {
        return $this->birth_date ?? $this->getBirthDateFromNIK();
    }

    /**
     * Get NIK region code
     */
    public function getRegionCode(): ?string
    {
        return NIKHelper::getRegionCode($this->nik);
    }

    /**
     * Get formatted NIK for display
     */
    public function getFormattedNIK(): string
    {
        return NIKHelper::format($this->nik);
    }

    /**
     * Get masked NIK for privacy
     */
    public function getMaskedNIK(): string
    {
        return NIKHelper::mask($this->nik);
    }

    /**
     * Get all NIK information
     */
    public function getNIKInfo(): NIKData
    {
        return NIKHelper::extractAll($this->nik);
    }

    /**
     * Check if NIK is valid
     */
    public function hasValidNIK(): bool
    {
        return NIKHelper::isValid($this->nik);
    }
}
