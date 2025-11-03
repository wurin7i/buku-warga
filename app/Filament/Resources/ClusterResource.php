<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClusterResource\Pages\CreateCluster;
use App\Filament\Resources\ClusterResource\Pages\EditCluster;
use App\Filament\Resources\ClusterResource\Pages\ListClusters;
use App\Filament\Resources\ClusterResource\RelationManagers\PropertiesRelationManager;
use App\Models\Cluster;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components as FormComponents;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns as TableColumns;
use Filament\Tables\Table;

class ClusterResource extends Resource
{
    protected static ?string $model = Cluster::class;

    public static function getNavigationIcon(): ?string
    {
        return 'gmdi-domain';
    }

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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            PropertiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClusters::route('/'),
            'create' => CreateCluster::route('/create'),
            'edit' => EditCluster::route('/{record}/edit'),
        ];
    }
}
