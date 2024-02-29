<?php

namespace App\Enums\PersonAttributes;

use App\Enums\Concerns\HasOptions;

enum Citizenship: string
{
    use HasOptions;

    case WNI = 'wni';
    case WNA = 'wna';
}