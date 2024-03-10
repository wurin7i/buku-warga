<?php

namespace App\Filament\Resources\CommunityAreaResource\Pages;

use App\Filament\Resources\CommunityAreaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCommunityAreas extends ListRecords
{
    protected static string $resource = CommunityAreaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
