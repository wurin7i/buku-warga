<?php

namespace App\Filament\Resources;

use App\Enums\AreaAttributes\Level as AreaLevel;
use App\Filament\Resources\PropertyResource\Pages;
use App\Filament\Resources\PropertyResource\RelationManagers;
use App\Models\Area;
use App\Models\Property;
use Filament\Forms;
use Filament\Forms\Components as FormComponents;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Monolog\Level;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FormComponents\Select::make('resident_community_id')
                    ->options(Area::applyLevel(AreaLevel::ResidentCommunity)->pluck('label', 'id'))
                    ->required()
                    ->live(),
                FormComponents\Select::make('administrative_area_id')
                    ->disabled(fn (Get $get) => !$get('resident_community_id'))
                    ->options(fn (Get $get) => $get('resident_community_id') ? Area::find($get('resident_community_id'))->children()->pluck('label', 'id') : [])
                    ->required()
                    ->live(),
                FormComponents\Select::make('area_id')
                    ->disabled(fn (Get $get) => !$get('administrative_area_id'))
                    ->options(fn (Get $get) => $get('administrative_area_id') ? Area::find($get('administrative_area_id'))->children()->pluck('label', 'id') : [])
                    ->columnSpan(2)
                    ->required(),
            ])->columns(4);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
