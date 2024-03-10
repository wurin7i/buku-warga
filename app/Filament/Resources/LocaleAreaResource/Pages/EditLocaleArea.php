<?php

namespace App\Filament\Resources\LocaleAreaResource\Pages;

use App\Filament\Resources\LocaleAreaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLocaleArea extends EditRecord
{
    protected static string $resource = LocaleAreaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
