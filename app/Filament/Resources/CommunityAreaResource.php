<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommunityAreaResource\Pages;
use App\Filament\Resources\CommunityAreaResource\RelationManagers;
use App\Models\CommunityArea;
use Filament\Forms\Components as FormComponents;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns as TableColumns;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CommunityAreaResource extends Resource
{
    protected static ?string $model = CommunityArea::class;

    protected static ?string $navigationIcon = 'gmdi-domain';

    protected static ?string $navigationGroup = 'Areas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FormComponents\TextInput::make('label')
                    ->columnSpan(2),
                FormComponents\TextInput::make('code')
                    ->columnSpan(1),
                FormComponents\Select::make('base_area_id')
                    ->relationship(name: 'base_area', titleAttribute: 'label')
                    ->columnSpan(1),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TableColumns\TextColumn::make('label'),
                TableColumns\TextColumn::make('code'),
                TableColumns\TextColumn::make('base_area.label'),
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
            'index' => Pages\ListCommunityAreas::route('/'),
            'create' => Pages\CreateCommunityArea::route('/create'),
            'edit' => Pages\EditCommunityArea::route('/{record}/edit'),
        ];
    }
}
