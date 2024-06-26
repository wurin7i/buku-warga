<?php

namespace App\Filament\Resources;

use App\Enums\AreaAttributes\Level as AreaLevel;
use App\Filament\Resources\PropertyResource\Pages;
use App\Filament\Resources\PropertyResource\RelationManagers;
use App\Models\Area;
use App\Models\Cluster;
use App\Models\SubRegion;
use App\Models\Property;
use Filament\Forms;
use Filament\Forms\Components as FormComponents;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static ?string $navigationIcon = 'gmdi-home-work-o';

    protected static ?string $recordTitleAttribute = 'label';

    public static function getModelLabel(): string
    {
        return __('property.resource_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FormComponents\Select::make('region_rw_id')
                    ->label(__('property.Region_RW'))
                    ->options(SubRegion::rwOnly()->pluck('name', 'id'))
                    ->afterStateHydrated(function (FormComponents\Select $select, string $operation) use ($form) {
                        if ($operation !== 'create' ) {
                            $select->state($form->getModelInstance()->sub_region->parent->getKey());
                        }
                    })
                    ->native(false)
                    ->required()
                    ->dehydrated(false)
                    ->live(),
                FormComponents\Select::make('sub_region_id')
                    ->label(__('property.Region_RT'))
                    ->relationship('sub_region', 'name', fn (Get $get, SubRegion $q) => ($parentId = $get('region_rw_id')) ? $q->applyParent($parentId) : $q)
                    ->disabled(fn (Get $get) => !$get('region_rw_id'))
                    // ->native(false)
                    ->required(),
                FormComponents\Select::make('cluster_id')
                    ->label(__('property.Cluster'))
                    ->relationship('cluster', 'name')
                    ->disabled(fn (Get $get) => !$get('region_rw_id'))
                    // ->native(false)
                    ->columnSpan(2),
                FormComponents\TextInput::make('label')
                    ->label(__('property.Label'))
                    ->required()
                    ->columnSpan(2),
                FormComponents\Select::make('owner_id')
                    ->relationship('owner', 'name')
                    ->native(false)
                    ->createOptionForm([
                        FormComponents\TextInput::make('name')
                    ])
                    ->columnSpan(2),
            ])->columns(4);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')
                    ->label(__('property.Label')),
                TextColumn::make('cluster.name')
                    ->label(__('property.Cluster')),
                TextColumn::make('sub_region.name')
                    ->label(__('property.Sub_Region')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProperties::route('/'),
            'create' => Pages\CreateProperty::route('/create'),
            'edit' => Pages\EditProperty::route('/{record}/edit'),
        ];
    }
}
