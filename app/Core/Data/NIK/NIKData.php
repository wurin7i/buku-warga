<?php

namespace App\Core\Data\NIK;

use Carbon\Carbon;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use WuriN7i\IdRefs\Enums\Gender;

#[MapName(SnakeCaseMapper::class)]
class NIKData extends Data
{
    public function __construct(
        public readonly string $nik,
        public readonly bool $isValid,
        public readonly ?string $regionCode,
        public readonly ?Carbon $birthDate,
        public readonly ?Gender $gender,
        public readonly ?int $age,
    ) {}

    /**
     * Create NIKData from NIK string
     */
    public static function fromNIK(string $nik): self
    {
        $helper = new \App\Core\Helpers\NIKHelper();

        return new self(
            nik: $nik,
            isValid: $helper::isValid($nik),
            regionCode: $helper::getRegionCode($nik),
            birthDate: $helper::extractBirthDate($nik),
            gender: $helper::extractGender($nik),
            age: $helper::calculateAge($nik),
        );
    }
}
