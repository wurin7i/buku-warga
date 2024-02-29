<?php

namespace App\Enums\PersonAttributes;

use App\Enums\Concerns\HasOptions;

enum Religion: string
{
    use HasOptions;

    case Islam = 'islam';
    case Protestan = 'protestan';
    case Katolik = 'katolik';
    case Hindu = 'hindu';
    case Buddha = 'buddha';
    case Khonghucu = 'khonghucu';
}