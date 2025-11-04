<?php

namespace App\Core\Data\Occupancy;

use App\Models\Occupant;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Carbon\Carbon;

#[MapName(SnakeCaseMapper::class)]
class OccupancyData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $personId,
        public readonly int $buildingId,
        public readonly bool $isResident,

        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public readonly ?Carbon $movedInDate,

        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public readonly ?Carbon $movedOutDate,

        public readonly bool $isCurrent,

        #[WithCast(DateTimeInterfaceCast::class)]
        public readonly ?Carbon $createdAt,

        #[WithCast(DateTimeInterfaceCast::class)]
        public readonly ?Carbon $updatedAt,
    ) {}

    public static function fromModel(Occupant $occupant): self
    {
        return new self(
            id: $occupant->id,
            personId: $occupant->person_id,
            buildingId: $occupant->building_id,
            isResident: $occupant->is_resident,
            movedInDate: $occupant->moved_in_date,
            movedOutDate: $occupant->moved_out_date,
            isCurrent: is_null($occupant->moved_out_date) || $occupant->moved_out_date->isFuture(),
            createdAt: $occupant->created_at,
            updatedAt: $occupant->updated_at,
        );
    }

    public function toModel(): Occupant
    {
        return new Occupant([
            'person_id' => $this->personId,
            'building_id' => $this->buildingId,
            'is_resident' => $this->isResident,
            'moved_in_date' => $this->movedInDate,
            'moved_out_date' => $this->movedOutDate,
        ]);
    }

    /**
     * Get occupancy duration in days
     */
    public function getDurationInDays(): ?int
    {
        if (!$this->movedInDate) {
            return null;
        }

        $endDate = $this->movedOutDate ?? now();
        return $this->movedInDate->diffInDays($endDate);
    }
}
