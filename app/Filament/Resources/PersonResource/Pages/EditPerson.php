<?php

namespace App\Filament\Resources\PersonResource\Pages;

use App\Core\Contracts\PersonServiceInterface;
use App\Core\Data\Person\UpdatePersonData;
use App\Filament\Resources\PersonResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditPerson extends EditRecord
{
    protected static string $resource = PersonResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $personService = app(PersonServiceInterface::class);

        $updatePersonData = UpdatePersonData::from($data);

        $personService->update($record->id, $updatePersonData);

        return $record->fresh();
    }

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
                ->action(fn() => info($this->record->declareDeath())),
            DeleteAction::make(),
        ];
    }
}
