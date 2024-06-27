<?php

namespace App\Filament\Resources\PersonResource\Pages;

use App\Filament\Resources\PersonResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class ListPeople extends ListRecords
{
    protected static string $resource = PersonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'resident' => Tab::make(__('person.Resident_List'))
                ->modifyQueryUsing(
                    fn (Builder $query) => $query->applyIsAlive()->applyIsResident(true)
                )
                ->icon('gmdi-face-retouching-natural'),
            'non_resident' => Tab::make(__('person.Non_Resident_List'))
                ->modifyQueryUsing(
                    fn (Builder $query) => $query->applyIsAlive()->applyIsResident(false)
                )
                ->icon('gmdi-face-s'),
            'death' => Tab::make(__('person.Death_List'))
                ->modifyQueryUsing(
                    fn (Builder $query) => $query->applyIsDeceased(true)
                )->icon('gmdi-face-retouching-off-o'),
        ];
    }
}
