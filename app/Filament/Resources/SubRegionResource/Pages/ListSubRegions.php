<?php

namespace App\Filament\Resources\SubRegionResource\Pages;

use App\Filament\Resources\SubRegionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubRegions extends ListRecords
{
    protected static string $resource = SubRegionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
