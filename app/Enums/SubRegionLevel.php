<?php

namespace App\Enums;

enum SubRegionLevel: int implements Comparable
{
    use Concerns\Operations;

    case VILLAGE = 0;
    case RW = 1;
    case RT = 2;

    public function label(): string
    {
        // TODO: customable name of sub-regions
        return match ($this) {
            self::VILLAGE => 'Kalurahan',
            self::RW => 'Padukuhan',
            self::RT => 'Rukun Tetangga',
        };
    }
}