<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonResource\Pages;
use App\Filament\Resources\PersonResource\RelationManagers;
use App\Models\Person;
use Filament\Forms\Components as FormComponents;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns as TableColumns;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use WuriN7i\IdRefs\Enums\Religion as EnumsReligion;
use WuriN7i\IdRefs\Models\BloodType;
use WuriN7i\IdRefs\Models\Gender;
use WuriN7i\IdRefs\Models\Religion;

class PersonResource extends Resource
{
    protected static ?string $model = Person::class;

    protected static ?string $navigationIcon = 'gmdi-people-alt-tt';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FormComponents\TextInput::make('nik')->required()
                    ->label('NIK')->columnSpan(2),
                FormComponents\TextInput::make('kk_number')
                    ->label('Nomor KK')->columnSpan(2),
                FormComponents\TextInput::make('name')->required()
                    ->columnSpan(3),
                FormComponents\Radio::make('gender_id')->required()
                    ->options(Gender::getArrayOptions())
                    ->columnSpan(2)
                    ->inline()->inlineLabel(false),
                FormComponents\TextInput::make('birth_place')->columnSpan(2),
                FormComponents\DatePicker::make('birth_date')
                    ->columnSpan(1)
                    ->native(false),
                FormComponents\Checkbox::make('is_deceased')->columnSpan(2),
                FormComponents\Select::make('blood_type_id')
                    ->relationship('bloodType', 'label')
                    ->native(false),
                FormComponents\Select::make('religion_id')
                    ->relationship('religion', 'label', fn (Builder $query) => $query->orderBy('sort_order'))
                    ->default(Religion::fromEnum(EnumsReligion::Islam)->getKey())
                    ->native(false),
                FormComponents\Select::make('marital_id')
                    ->relationship('marital', 'label')
                    ->native(false),
                FormComponents\Select::make('citizenship_id')->required()
                    ->default(13)
                    ->relationship('citizenship', 'label')
                    ->native(false),
                FormComponents\Select::make('occupation_id')->columnSpan(2)
                    ->relationship('occupation', 'label')
                    ->searchable()
                    ->createOptionForm([
                        FormComponents\TextInput::make('label')
                            ->required(),
                    ])
                    ->native(false),
                FormComponents\Checkbox::make('is_occupy')
                    ->afterStateHydrated(function (FormComponents\Checkbox $checkbox) use ($form) {
                        if ($form->model instanceof Person) {
                            $checkbox->state($form->model->is_occupy);
                        }
                    })->live(),
                FormComponents\Fieldset::make('Penghuni')
                    ->hidden(fn (Get $get) => !$get('is_occupy'))
                    ->relationship('occupy')
                    ->schema([
                        FormComponents\Select::make('building_id')
                            ->relationship('building', 'label')->native(false),
                        FormComponents\Checkbox::make('is_resident'),
                        FormComponents\DatePicker::make('moved_in_at')->native(false),
                    ])
            ])->columns(5);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TableColumns\TextColumn::make('name')->searchable(),
                TableColumns\TextColumn::make('nik'),
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
