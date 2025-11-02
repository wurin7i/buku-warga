<?php

namespace App\Enums;

use App\Enums\Concerns\Operations;

enum AreaType: string implements Comparable
{
    use Operations;

    case SubRegion = 'sub-region';
    case Cluster = 'cluster';
}
