<?php

namespace App\Filament\Resources\ClusterResource\RelationManagers;

use App\Enums\SubRegionLevel;
use App\Models\Cluster;
use App\Models\SubRegion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PropertiesRelationManager extends RelationManager
{
    protected static string $relationship = 'properties';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('label')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('sub_region_id')
                    ->relationship(
                        name: 'subRegion',
                        titleAttribute: 'name',
                        modifyQueryUsing: function (SubRegion $q) {
                            /** @var SubRegion */
                            $baseArea = $this->getOwnerRecord()->baseArea;
                            return $baseArea->level->is(SubRegionLevel::RT)
                                ? $q->whereKey($baseArea->id)
                                : $q->applyParent($baseArea);
                        }
                    ),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->sortable(),
            ])
            ->inverseRelationship('cluster')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                // TODO: show only properies with RT matches baseArea
                Tables\Actions\AssociateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
