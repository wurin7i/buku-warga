<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LocaleAreaResource\Pages;
use App\Filament\Resources\LocaleAreaResource\RelationManagers;
use App\Filament\Resources\LocaleAreaResource\RelationManagers\ChildrenRelationManager;
use App\Models\LocaleArea;
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

class LocaleAreaResource extends Resource
{
    protected static ?string $model = LocaleArea::class;

    protected static ?string $navigationIcon = 'gmdi-account-tree-o';

    protected static ?string $navigationGroup = 'Areas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FormComponents\TextInput::make('label')->columnSpan(2),
                FormComponents\TextInput::make('code')->columnSpan(1),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TableColumns\TextColumn::make('label')->searchable(),
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
                Group::make('parent.label')
                    ->titlePrefixedWithLabel(false)
            ])
            ->defaultGroup('parent.label')
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
            'index' => Pages\ListLocaleAreas::route('/'),
            'create' => Pages\CreateLocaleArea::route('/create'),
            'edit' => Pages\EditLocaleArea::route('/{record}/edit'),
        ];
    }
}
