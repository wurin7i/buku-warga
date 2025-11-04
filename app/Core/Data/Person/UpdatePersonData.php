<?php

namespace App\Core\Data\Person;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Carbon\Carbon;

#[MapName(SnakeCaseMapper::class)]
class UpdatePersonData extends Data
{
    public function __construct(
        #[Max(255)]
        public readonly ?string $name = null,

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

        public readonly ?bool $isDeceased = null,
    ) {}

    /**
     * Get only non-null values for update
     */
    public function getNonNullValues(): array
    {
        return array_filter($this->toArray(), function ($value) {
            return $value !== null;
        });
    }
}
