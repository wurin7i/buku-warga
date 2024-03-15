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

    protected static ?string $navigationGroup = 'Areas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FormComponents\TextInput::make('name')
                    ->autocomplete(false)
                    ->columnSpan(2),
                FormComponents\Select::make('parent_id')
                    ->relationship(name: 'parent', titleAttribute: 'name')
                    ->native(false)
                    ->columnSpan(1),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TableColumns\TextColumn::make('name')->searchable(),
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
                    ->titlePrefixedWithLabel(false)
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