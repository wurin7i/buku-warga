<?php

namespace App\Filament\Resources\PersonResource\Pages;

use App\Core\Contracts\PersonServiceInterface;
use App\Core\Data\Person\CreatePersonData;
use App\Filament\Resources\PersonResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreatePerson extends CreateRecord
{
    protected static string $resource = PersonResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $personService = app(PersonServiceInterface::class);

        $createPersonData = CreatePersonData::from($data);

        return $personService->createModel($createPersonData);
    }
}
