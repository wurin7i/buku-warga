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
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use WuriN7i\IdRefs\Enums\Citizenship as EnumsCitizenship;
use WuriN7i\IdRefs\Enums\Religion as EnumsReligion;
use WuriN7i\IdRefs\Models\Citizenship;
use WuriN7i\IdRefs\Models\Gender;
use WuriN7i\IdRefs\Models\Region;
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
                FormComponents\TextInput::make('name')
                    ->label(__('person.Name'))
                    ->required()
                    ->columnSpan(3),
                FormComponents\TextInput::make('birth_place')
                    ->label(__('person.Place_of_Birth'))
                    ->columnSpan(2),
                FormComponents\DatePicker::make('birth_date')
                    ->label(__('person.Date_of_Birth'))
                    ->displayFormat('d/m/Y')
                    ->placeholder('hh/bb/tttt')
                    ->native(false),
                FormComponents\Select::make('gender')
                    ->label(__('person.Gender'))
                    ->required()
                    ->relationship('gender', 'label')
                    ->options(Gender::getArrayOptions())
                    ->columnSpan(1),
                FormComponents\Select::make('bloodType')
                    ->label(__('person.Blood_Type'))
                    ->relationship('bloodType', 'label')
                    ->native(false),
                FormComponents\Select::make('religion')
                    ->label(__('person.Religion'))
                    ->relationship('religion', 'label', fn (Builder $query) => $query->orderBy('sort_order'))
                    ->default(Religion::fromEnum(EnumsReligion::Islam)->getKey())
                    ->native(false),
                FormComponents\Select::make('marital')
                    ->label(__('person.Marital'))
                    ->relationship('marital', 'label')
                    ->native(false),
                FormComponents\Select::make('occupation')
                    ->label(__('person.Occupation'))
                    ->relationship('occupation', 'label')
                    ->searchable()
                    ->createOptionForm([
                        FormComponents\TextInput::make('label')
                            ->required(),
                    ])
                    ->columnSpan(2)
                    ->native(false),
                FormComponents\TextInput::make('address')
                    ->label(__('person.Address'))
                    ->columnSpan(2),
                FormComponents\TextInput::make('sub_region')
                    ->label(__('person.Sub_Region'))
                    ->placeholder('000/000')
                    ->mask('999/999'),
                FormComponents\Select::make('region')
                    ->label(__('person.Region'))
                    ->relationship('region', 'name', fn (Builder $query) => $query->with('parent'))
                    ->getOptionLabelFromRecordUsing(fn (Region $r) => "{$r->name}, {$r->parent->name}")
                    ->searchable(['name'])
                    ->native(false)
                    ->columnSpan(2),
                FormComponents\Select::make('citizenship')
                    ->label(__('person.Citizenship'))
                    ->required()
                    ->default(Citizenship::fromEnum(EnumsCitizenship::WNI)->getKey())
                    ->relationship('citizenship', 'label')
                    ->native(false),
                FormComponents\Tabs::make('Tabs')
                    ->tabs([
                        FormComponents\Tabs\Tab::make(__('person.Residence'))
                            ->schema([
                                // FormComponents\Checkbox::make('is_occupying')
                                //     ->label(__('person.Is_Occupying'))
                                //     ->disabled(fn (Get $get) => $get('is_deceased'))
                                //     ->afterStateHydrated(function (FormComponents\Checkbox $checkbox, Person $person) {
                                //         $checkbox->state($person->is_occupying);
                                //     })
                                //     ->dehydrated(false)
                                //     ->live(),
                                FormComponents\Grid::make('occupy')
                                    ->label('Penghuni')
                                    ->disabled(fn (Get $get) => /* !$get('is_occupying') || */ $get('is_deceased'))
                                    ->relationship('occupy')
                                    ->schema([
                                        FormComponents\Select::make('building_id')
                                            ->label(__('person.Building'))
                                            ->relationship('building', 'label')
                                            ->native(false),
                                        FormComponents\DatePicker::make('moved_in_at')
                                            ->label(__('person.Date_of_Move_In'))
                                            ->native(false),
                                        FormComponents\Checkbox::make('is_resident')
                                            ->label(__('person.Is_Resident')),
                                    ])->columns(3)
                                    ->key('occupyFields'),
                            ]),
                        FormComponents\Tabs\Tab::make(__('person.Family'))
                            ->schema([
                                FormComponents\TextInput::make('kk_number')
                                    ->label(__('person.KK_Number')),
                                FormComponents\TextInput::make('_role')
                                    ->label(__('person.Family_Role'))
                                    ->disabled(true),
                                FormComponents\TextInput::make('_father')
                                    ->label(__('person.Father'))
                                    ->disabled(true),
                                FormComponents\TextInput::make('_mother')
                                    ->label(__('person.Mother'))
                                    ->disabled(true),

                            ])->columns(2)
                    ])->columnSpanFull(),
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
                // TODO: show alive only
                // Filter::make('is_alive'),
            ], layout: FiltersLayout::Modal)
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
