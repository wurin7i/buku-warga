<?php

namespace App\Filament\Resources\PersonResource\Pages;

use App\Filament\Resources\PersonResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\EditRecord;

class EditPerson extends EditRecord
{
    protected static string $resource = PersonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('declare_death')
                ->color('warning')
                ->label(__('person.Declare_Death'))
                ->successRedirectUrl(PersonResource::getUrl())
                ->successNotificationTitle('Deleted')
                ->schema([
                    DatePicker::make('death_date')
                        ->native(false)
                        ->required(),
                ])
                ->requiresConfirmation(true)
                ->action(fn () => info($this->record->declareDeath())),
            DeleteAction::make(),
        ];
    }
}
