<?php

namespace App\Enums\PersonAttributes;

use App\Enums\Concerns\HasOptions;

enum Marriage: string
{
    use HasOptions;

    case Single = 'single';
    case Married = 'married';
    // case Divorced = 'single';
    // case Widowed = 'single';
    // case Widow = 'single';
}