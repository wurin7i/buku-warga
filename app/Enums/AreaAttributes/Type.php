<?php

namespace App\Enums\AreaAttributes;

use App\Enums\Concerns\HasOptions;

enum Type: string
{
    use HasOptions;

    case Administrative = 'administrative';
    case NonAdministrative = 'non-administrative';
}