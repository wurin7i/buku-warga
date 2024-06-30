<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClusterResource\Pages;
use App\Filament\Resources\ClusterResource\RelationManagers;
use App\Filament\Resources\ClusterResource\RelationManagers\PropertiesRelationManager;
use App\Models\Cluster;
use Filament\Forms\Components as FormComponents;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns as TableColumns;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClusterResource extends Resource
{
    protected static ?string $model = Cluster::class;

    protected static ?string $navigationIcon = 'gmdi-domain';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): string
    {
        return __('area.navigation_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('cluster.resource_label');
    }

    public static function getModelLabel(): string
    {
        return __('cluster.resource_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FormComponents\TextInput::make('name')
                    ->label(__('cluster.Name'))
                    ->columnSpan(2),
                FormComponents\Select::make('parent_id')
                    ->label(__('cluster.Base_Area'))
                    ->relationship(name: 'baseArea', titleAttribute: 'name')
                    ->native(false),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TableColumns\TextColumn::make('name')
                    ->label(__('cluster.Name')),
                TableColumns\TextColumn::make('baseArea.name')
                    ->label(__('cluster.Base_Area')),
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
            PropertiesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClusters::route('/'),
            'create' => Pages\CreateCluster::route('/create'),
            'edit' => Pages\EditCluster::route('/{record}/edit'),
        ];
    }
}
