<?php

namespace App\Enums\AreaAttributes;

use App\Enums\Concerns\HasOptions;

enum Level: int
{
    case Province = 1;
    case Regency = 2;
    case District = 3;
    case Village = 4;
    case ResidentCommunity = 5;
    case NeighbourCommunity = 6;

    public function label(): string
    {
        return match ($this) {
            self::Province => 'Provinsi',
            self::Regency => 'Kabupaten/Kota',
            self::District => 'Kecamatan',
            self::Village => 'Desa/Kelurahan',
            self::ResidentCommunity => 'RW',
            self::NeighbourCommunity => 'RT',
        };
    }

    public function getLowerLevel(): ?self
    {
        return match ($this) {
            self::Province => self::Regency,
            self::Regency => self::District,
            self::District => self::Village,
            self::Village => self::ResidentCommunity,
            self::ResidentCommunity => self::NeighbourCommunity,
            self::NeighbourCommunity => null,
        };
    }
}
