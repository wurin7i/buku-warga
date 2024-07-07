<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubRegionResource\Pages;
use App\Filament\Resources\SubRegionResource\RelationManagers;
use App\Filament\Resources\SubRegionResource\RelationManagers\ChildrenRelationManager;
use App\Models\SubRegion;
use Filament\Forms\Components as FormComponents;
use Filament\Forms;
use Filament\Forms\Form;
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

    protected static ?string $navigationIcon = 'gmdi-account-tree-o';

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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
                    ->relationship(name: 'parent', titleAttribute: 'name')
                    ->disabledOn('edit')
                    ->native(false),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TableColumns\TextColumn::make('sub_region_name')
                    ->label(__('sub_region.Name'))
                    ->searchable('areas.name'),
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
            ])->groups([
                Group::make('parent.name')
                    ->titlePrefixedWithLabel(false),
            ])
            ->defaultGroup('parent.name')
            ->groupingSettingsHidden();
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
            'index' => Pages\ListSubRegions::route('/'),
            'create' => Pages\CreateSubRegion::route('/create'),
            'edit' => Pages\EditSubRegion::route('/{record}/edit'),
        ];
    }
}
