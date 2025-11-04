<?php

namespace App\Core\Data\Person;

use App\Core\Data\NIK\NIKData;
use App\Core\Helpers\NIKHelper;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Regex;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Carbon\Carbon;

#[MapName(SnakeCaseMapper::class)]
class CreatePersonData extends Data
{
    public function __construct(
        #[Required, Regex('/^\d{16}$/')]
        public readonly string $nik,

        #[Required, Max(255)]
        public readonly string $name,

        #[Max(16)]
        public readonly ?string $kkNumber = null,

        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public readonly ?Carbon $birthDate = null,

        #[Max(255)]
        public readonly ?string $birthPlace = null,

        public readonly ?string $address = null,

        #[Max(7)]
        public readonly ?string $subRegion = null,

        public readonly ?string $notes = null,

        // Optional relations for creation
        public readonly ?int $propertyId = null,
        public readonly ?bool $isResident = true,

        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public readonly ?Carbon $movedInDate = null,
    ) {}

    /**
     * Get gender from NIK
     */
    public function getGender(): ?string
    {
        return NIKHelper::extractGender($this->nik);
    }

    /**
     * Get effective birth date (prefer provided, fallback to NIK)
     */
    public function getEffectiveBirthDate(): ?Carbon
    {
        return $this->birthDate ?? NIKHelper::extractBirthDate($this->nik);
    }

    /**
     * Get NIK region code
     */
    public function getRegionCode(): string
    {
        return NIKHelper::getRegionCode($this->nik) ?? '';
    }

    /**
     * Check if NIK is valid
     */
    public function hasValidNIK(): bool
    {
        return NIKHelper::isValid($this->nik);
    }

    /**
     * Get all NIK information for validation
     */
    public function getNIKInfo(): NIKData
    {
        return NIKHelper::extractAll($this->nik);
    }

    /**
     * Validate NIK and throw exception if invalid
     */
    public function validateNIK(): void
    {
        NIKHelper::validate($this->nik);
    }
}
