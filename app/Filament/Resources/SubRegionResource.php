<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\SubRegionResource\Pages\ListSubRegions;
use App\Filament\Resources\SubRegionResource\Pages\CreateSubRegion;
use App\Filament\Resources\SubRegionResource\Pages\EditSubRegion;
use App\Filament\Resources\SubRegionResource\Pages;
use App\Filament\Resources\SubRegionResource\RelationManagers;
use App\Filament\Resources\SubRegionResource\RelationManagers\ChildrenRelationManager;
use App\Models\SubRegion;
use Filament\Forms\Components as FormComponents;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables\Columns as TableColumns;
use Filament\Tables;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubRegionResource extends Resource
{
    protected static ?string $model = SubRegion::class;

    public static function getNavigationIcon(): ?string
    {
        return 'gmdi-account-tree-o';
    }

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): string
    {
        return __('area.navigation_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('sub_region.resource_label');
    }

    public static function getModelLabel(): string
    {
        return __('sub_region.resource_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FormComponents\TextInput::make('name')
                    ->label(__('sub_region.Name'))
                    ->required()
                    ->autocomplete(false)
                    ->columnSpan(2),
                FormComponents\Select::make('parent')
                    ->label(__('sub_region.Parent'))
                    ->required()
                    ->searchable()
                    ->preload()
                    ->relationship(
                        name: 'parent',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn(Builder $query) => $query->withoutGlobalScopes()
                    )
                    ->disabledOn('edit')
                    ->native(false),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $query->with(['parent' => function ($query) {
                    $query->withoutGlobalScopes();
                }]);
            })
            ->columns([
                TableColumns\TextColumn::make('name')
                    ->label(__('sub_region.Name'))
                    ->searchable(),
                TableColumns\TextColumn::make('parent_name')
                    ->label(__('sub_region.Parent'))
                    ->sortable(false)
                    ->searchable(false),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('parent_id')
                    ->label(__('sub_region.Parent'))
                    ->relationship('parent', 'name', fn(Builder $query) => $query->withoutGlobalScopes())
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->groups([
                Group::make('parent_name')
                    ->label('Parent')
                    ->titlePrefixedWithLabel(false),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ChildrenRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSubRegions::route('/'),
            'create' => CreateSubRegion::route('/create'),
            'edit' => EditSubRegion::route('/{record}/edit'),
        ];
    }
}
