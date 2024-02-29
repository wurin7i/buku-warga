<?php

namespace App\Filament\Resources;

use App\Enums\PersonAttributes\BloodType;
use App\Enums\PersonAttributes\Gender;
use App\Filament\Resources\PersonResource\Pages;
use App\Filament\Resources\PersonResource\RelationManagers;
use App\Models\Person;
use Filament\Forms\Components as FormComponents;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns as TableColumns;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PersonResource extends Resource
{
    protected static ?string $model = Person::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FormComponents\TextInput::make('nik')->label('NIK'),
                FormComponents\TextInput::make('kk_number')->label('Nomor KK')->columnSpan(2),
                FormComponents\TextInput::make('name')->columnSpan(2),
                FormComponents\TextInput::make('birth_place'),
                FormComponents\DatePicker::make('birth_date'),
                FormComponents\Radio::make('gender')->options(Gender::getArrayOptions())
                    ->inline()
                    ->inlineLabel(false),
                FormComponents\Select::make('blood_type')->options(BloodType::getArrayOptions()),
                FormComponents\Checkbox::make('is_deceased'),
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
            'index' => Pages\ListPeople::route('/'),
            'create' => Pages\CreatePerson::route('/create'),
            'edit' => Pages\EditPerson::route('/{record}/edit'),
        ];
    }
}
