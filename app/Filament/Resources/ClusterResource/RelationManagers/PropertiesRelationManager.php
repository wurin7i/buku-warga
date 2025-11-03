<?php

namespace App\Filament\Resources\ClusterResource\RelationManagers;

use App\Enums\SubRegionLevel;
use App\Models\SubRegion;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PropertiesRelationManager extends RelationManager
{
    protected static string $relationship = 'properties';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('label')
                    ->required()
                    ->maxLength(255),
                Select::make('sub_region_id')
                    ->relationship(
                        name: 'subRegion',
                        titleAttribute: 'name',
                        modifyQueryUsing: function (Builder $query) {
                            /** @var SubRegion */
                            $baseArea = $this->getOwnerRecord()->baseArea;

                            return $baseArea->level->is(SubRegionLevel::RT)
                                ? $query->whereKey($baseArea->id)
                                : $query->applyParent($baseArea);
                        }
                    ),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                TextColumn::make('label')
                    ->sortable(),
            ])
            ->inverseRelationship('cluster')
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
                // TODO: show only properies with RT matches baseArea
                AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
