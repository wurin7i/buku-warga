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
use WuriN7i\IdRefs\Enums\Citizenship as EnumsCitizenship;
use WuriN7i\IdRefs\Enums\Religion as EnumsReligion;
use WuriN7i\IdRefs\Models\Citizenship;
use WuriN7i\IdRefs\Models\Gender;
use WuriN7i\IdRefs\Models\Religion;

class PersonResource extends Resource
{
    protected static ?string $model = Person::class;

    protected static ?string $navigationIcon = 'gmdi-people-alt-tt';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return __('person.resource_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FormComponents\TextInput::make('nik')->required()
                    ->label(__('person.NIK'))
                    ->columnSpan(2),
                FormComponents\TextInput::make('kk_number')
                    ->label(__('person.KK_Number'))
                    ->columnSpan(2),
                FormComponents\TextInput::make('name')
                    ->label(__('person.Name'))
                    ->required()
                    ->columnSpan(3),
                FormComponents\Radio::make('gender_id')
                    ->label(__('person.Gender'))
                    ->required()
                    ->options(Gender::getArrayOptions())
                    ->columnSpan(2)
                    ->inline()->inlineLabel(false),
                FormComponents\TextInput::make('birth_place')
                    ->label(__('person.Place_of_Birth'))
                    ->columnSpan(2),
                FormComponents\DatePicker::make('birth_date')
                    ->label(__('person.Date_of_Birth'))
                    ->columnSpan(1)
                    ->native(false),
                FormComponents\Checkbox::make('is_deceased')
                    ->label(__('person.Is_Deceased'))
                    ->columnSpan(2)
                    ->live(),
                FormComponents\Select::make('blood_type_id')
                    ->label(__('person.Blood_Type'))
                    ->relationship('bloodType', 'label')
                    ->disabled(fn (Get $get) => $get('is_deceased'))
                    ->native(false),
                FormComponents\Select::make('religion_id')
                    ->label(__('person.Religion'))
                    ->relationship('religion', 'label', fn (Builder $query) => $query->orderBy('sort_order'))
                    ->default(Religion::fromEnum(EnumsReligion::Islam)->getKey())
                    ->disabled(fn (Get $get) => $get('is_deceased'))
                    ->native(false),
                FormComponents\Select::make('marital_id')
                    ->label(__('person.Marital'))
                    ->relationship('marital', 'label')
                    ->disabled(fn (Get $get) => $get('is_deceased'))
                    ->native(false),
                FormComponents\Select::make('citizenship_id')
                    ->label(__('person.Citizenship'))
                    ->required()
                    ->default(Citizenship::fromEnum(EnumsCitizenship::WNI)->getKey())
                    ->relationship('citizenship', 'label')
                    ->disabled(fn (Get $get) => $get('is_deceased'))
                    ->native(false),
                FormComponents\Select::make('occupation_id')
                    ->label(__('person.Occupation'))
                    ->relationship('occupation', 'label')
                    ->searchable()
                    ->createOptionForm([
                        FormComponents\TextInput::make('label')
                            ->required(),
                    ])
                    ->columnSpan(2)
                    ->disabled(fn (Get $get) => $get('is_deceased'))
                    ->native(false),
                FormComponents\Checkbox::make('is_occupying')
                    ->label(__('person.Is_Occupying'))
                    ->disabled(fn (Get $get) => $get('is_deceased'))
                    ->afterStateHydrated(function (FormComponents\Checkbox $checkbox) {
                        if ($checkbox->getContainer()->model instanceof Person) {
                            $checkbox->state($checkbox->getContainer()->model->is_occupying);
                        }
                    })
                    ->dehydrated(false)
                    ->live(),
                FormComponents\Grid::make('occupy')
                    ->label('Penghuni')
                    ->visible(fn (Get $get) => $get('is_occupying') && !$get('is_deceased'))
                    ->relationship('occupy')
                    ->schema([
                        FormComponents\Select::make('building_id')
                            ->label(__('person.Building'))
                            ->relationship('building', 'label')
                            ->native(false),
                        FormComponents\Checkbox::make('is_resident')
                            ->label(__('person.Is_Resident')),
                        FormComponents\DatePicker::make('moved_in_at')
                            ->label(__('person.Date_of_Move_In'))
                            ->native(false),
                    ])->columns(2)
                    ->key('occupyFields'),
            ])->columns(5);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TableColumns\TextColumn::make('name')
                    ->label(__('person.Name'))
                    ->searchable(),
                TableColumns\TextColumn::make('nik')
                    ->toggleable()
                    ->label(__('person.NIK')),
                TableColumns\TextColumn::make('occupy.building.label')
                    ->toggleable()
                    ->label(__('person.Residence')),
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
