<?php

namespace App\Enums\PersonAttributes;

use App\Enums\Concerns\HasOptions;

enum BloodType: string
{
    use HasOptions;

    case O = 'oou';
    case ONegative = 'oon';
    case OPositive = 'oop';
    case A = 'oau';
    case ANegative = 'oan';
    case APositive = 'oap';
    case B = 'obu';
    case BNegative = 'obn';
    case BPositive = 'obp';
    case AB = 'abu';
    case ABNegative = 'abn';
    case ABPositive = 'abp';
}