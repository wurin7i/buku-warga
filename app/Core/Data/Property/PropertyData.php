<?php

namespace App\Core\Data\Property;

use App\Models\Property;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Carbon\Carbon;

#[MapName(SnakeCaseMapper::class)]
class PropertyData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $label,
        public readonly bool $hasBuilding,
        public readonly ?int $subRegionId,
        public readonly ?string $subRegionName,
        public readonly ?int $clusterId,
        public readonly ?string $clusterName,

        #[WithCast(DateTimeInterfaceCast::class)]
        public readonly ?Carbon $createdAt,

        #[WithCast(DateTimeInterfaceCast::class)]
        public readonly ?Carbon $updatedAt,
    ) {}

    public static function fromModel(Property $property): self
    {
        return new self(
            id: $property->id,
            label: $property->label,
            hasBuilding: $property->has_building,
            subRegionId: $property->sub_region_id,
            subRegionName: $property->subRegion?->name,
            clusterId: $property->cluster_id,
            clusterName: $property->cluster?->name,
            createdAt: $property->created_at,
            updatedAt: $property->updated_at,
        );
    }

    public function toModel(): Property
    {
        return new Property([
            'label' => $this->label,
            'has_building' => $this->hasBuilding,
            'sub_region_id' => $this->subRegionId,
            'cluster_id' => $this->clusterId,
        ]);
    }
}
