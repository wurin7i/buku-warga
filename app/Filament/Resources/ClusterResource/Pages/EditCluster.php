<?php

namespace App\Filament\Resources\ClusterResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\ClusterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCluster extends EditRecord
{
    protected static string $resource = ClusterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
