<?php

namespace App\Enums;

enum OccupancyType: string
{
    case OWNER = 'owner';
    case RENTER = 'renter';
    case FAMILY_MEMBER = 'family_member';
    case GUEST = 'guest';
}
