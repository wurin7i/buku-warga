<?php

namespace App\Filament\Resources\SubRegionResource\Pages;

use App\Filament\Resources\SubRegionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSubRegion extends EditRecord
{
    protected static string $resource = SubRegionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
