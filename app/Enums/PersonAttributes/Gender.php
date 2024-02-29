<?php

namespace App\Enums\PersonAttributes;

use App\Enums\Concerns\HasOptions;

enum Gender: string
{
    use HasOptions;

    case Male = 'male';
    case Female = 'female';
    // case Other = 'other';
    // case Unknown = 'unknown';

    public function label(): string
    {
        return match ($this) {
            self::Male => 'Laki-laki',
            self::Female => 'Perempuan',
        };
    }
}