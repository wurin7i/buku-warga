<?php

namespace App\Filament\Resources\SubRegionResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Schemas\Components\Tabs\Tab;
use App\Filament\Resources\SubRegionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListSubRegions extends ListRecords
{
    protected static string $resource = SubRegionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'village' => Tab::make(__('sub_region.Village'))
                ->modifyQueryUsing(fn (Builder $query) => $this->prepareQuery($query)->villageOnly()),
            'rw' => Tab::make(__('sub_region.Rw'))
                ->modifyQueryUsing(fn (Builder $query) => $this->prepareQuery($query)->rwOnly()),
            'rt' => Tab::make(__('sub_region.Rt'))
                ->modifyQueryUsing(fn (Builder $query) => $this->prepareQuery($query)->rtOnly()),
        ];
    }

    public function prepareQuery(Builder $builder): Builder
    {
        return $builder->leftJoin('areas as p', 'p.id', '=', 'areas.parent_id')
            ->select(['areas.*', 'p.name as parent_name', 'p.id as group_id'])
            ->orderBy('parent_name');
    }
}
