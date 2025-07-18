<?php

use App\Enums\SubRegionLevel;

return [
    'resource_label' => 'Daerah Administratif',
    'resource_children_title' => 'Daerah Di Bawah :level :name',

    // field labels
    'Name' => 'Nama',
    'Parent' => 'Induk',
    'Village' => SubRegionLevel::VILLAGE->label(),
    'Rw' => SubRegionLevel::RW->label(),
    'Rt' => SubRegionLevel::RT->label(),
];
