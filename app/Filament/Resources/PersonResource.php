<?php

namespace App\Filament\Resources;

use App\Core\Contracts\PersonServiceInterface;
use App\Core\Helpers\NIKHelper;
use App\Filament\Resources\PersonResource\Pages\CreatePerson;
use App\Filament\Resources\PersonResource\Pages\EditPerson;
use App\Filament\Resources\PersonResource\Pages\ListPeople;
use App\Models\Person;
use App\Models\Property;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components as FormComponents;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns as TableColumns;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use WuriN7i\IdRefs\Enums\Citizenship as EnumsCitizenship;
use WuriN7i\IdRefs\Enums\Religion as EnumsReligion;
use WuriN7i\IdRefs\Models\Citizenship;
use WuriN7i\IdRefs\Models\Gender;
use WuriN7i\IdRefs\Models\Region;
use WuriN7i\IdRefs\Models\Religion;

class PersonResource extends Resource
{
    protected static ?string $model = Person::class;

    public static function getNavigationIcon(): ?string
    {
        return 'gmdi-people-alt-tt';
    }

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return __('person.resource_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FormComponents\TextInput::make('nik')->required()
                    ->label(__('person.NIK'))
                    ->columnSpan(2)
                    ->regex('/^\d{16}$/')
                    ->mask('9999999999999999')
                    ->rules('digits:16')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, ?string $state) {
                        if (NIKHelper::isValid($state)) {
                            $gender = NIKHelper::extractGender($state);
                            $birthDate = NIKHelper::extractBirthDate($state);

                            $set('birth_date', $birthDate);
                            if ($gender) {
                                $set('gender', Gender::fromEnum($gender)->getKey());
                            }
                        }
                    }),
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
                    ->preload()
                    ->columnSpan(1),
                FormComponents\Select::make('bloodType')
                    ->label(__('person.Blood_Type'))
                    ->relationship('bloodType', 'label')
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
                    ->relationship(
                        'region',
                        'name',
                        fn(Builder $query) => $query->villageOnly()
                            ->with(['parent', 'parent.parent'])
                            ->join('ref_regions as p', 'p.id', '=', 'ref_regions.parent_id')
                            ->select('ref_regions.*')
                    )
                    ->getOptionLabelFromRecordUsing(
                        fn(Region $r) => "{$r->name}, {$r->parent->name}, {$r->parent->parent->name}"
                    )
                    ->searchable(['ref_regions.name', "concat_ws(' ', `ref_regions`.`name`, `p`.`name`)"])
                    ->native(false)
                    ->columnSpan(2),
                FormComponents\Select::make('religion')
                    ->label(__('person.Religion'))
                    ->relationship('religion', 'label', fn(Builder $query) => $query->orderBy('sort_order'))
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
                FormComponents\Select::make('citizenship')
                    ->label(__('person.Citizenship'))
                    ->required()
                    ->default(Citizenship::fromEnum(EnumsCitizenship::WNI)->getKey())
                    ->relationship('citizenship', 'label')
                    ->native(false),
                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make(__('person.Residence'))
                            ->schema([
                                FormComponents\Toggle::make('is_occupying')
                                    ->label(__('person.Is_Occupying'))
                                    ->live()
                                    ->afterStateHydrated(function (Toggle $component, $record) {
                                        if ($record && $record instanceof Person) {
                                            $component->state($record->is_occupying);
                                        }
                                    })
                                    ->dehydrated(false),
                                Actions::make([
                                    Action::make('move_out')
                                        ->color('warning')
                                        ->requiresConfirmation()
                                        ->schema([
                                            FormComponents\DatePicker::make('moved_out_date')
                                                ->beforeOrEqual(now())
                                                ->displayFormat('d/m/Y')
                                                ->placeholder('hh/bb/tttt')
                                                ->native(false)
                                                ->required(),
                                        ])
                                        ->fillForm(fn() => [
                                            'moved_out_date' => now(),
                                        ])
                                        ->action(function (array $data, $record, Set $set) {
                                            if ($record && $record instanceof Person) {
                                                $record->occupy?->moveOut(Carbon::create($data['moved_out_date']));
                                                $set('is_occupying', false);
                                            }
                                        })
                                        ->modalWidth(Width::Small)
                                        ->visible(function ($record, Get $get) {
                                            if (! $record || ! ($record instanceof Person)) {
                                                return $get('is_occupying');
                                            }

                                            return $record->is_occupying || ($get('is_occupying') && $record->exists);
                                        }),
                                ])
                                    ->alignEnd(),
                                Group::make()
                                    ->relationship('occupy')
                                    ->schema([
                                        FormComponents\Select::make('building_id')
                                            ->label(__('person.Building'))
                                            ->options(
                                                fn() => Property::with('cluster')->buildingOnly()->get()
                                                    ->groupBy('cluster.name')
                                                    ->transform(fn($rows) => $rows->pluck('label', 'id'))
                                            )
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->native(false)
                                            ->live(),
                                        FormComponents\DatePicker::make('moved_in_date')
                                            ->label(__('person.Date_of_Move_In'))
                                            ->disabled(fn(Get $get) => ! $get('building_id'))
                                            ->displayFormat('d/m/Y')
                                            ->placeholder('hh/bb/tttt')
                                            ->beforeOrEqual('today')
                                            ->native(false),
                                        FormComponents\Checkbox::make('is_resident')
                                            ->label(__('person.Is_Resident'))
                                            ->disabled(fn(Get $get) => ! $get('building_id')),
                                    ])
                                    ->columns(3)
                                    ->hidden(fn(Get $get) => ! $get('is_occupying'))
                                    ->key('occupyFields'),
                            ])
                            ->columns(2),
                        Tab::make(__('person.Family'))
                            ->schema([
                                FormComponents\TextInput::make('kk_number')
                                    ->label(__('person.KK_Number'))
                                    ->mask('9999999999999999')
                                    ->rules('digits:16'),
                                FormComponents\TextInput::make('_role')
                                    ->label(__('person.Family_Role'))
                                    ->disabled(true),
                                FormComponents\TextInput::make('_father')
                                    ->label(__('person.Father'))
                                    ->disabled(true),
                                FormComponents\TextInput::make('_mother')
                                    ->label(__('person.Mother'))
                                    ->disabled(true),
                            ])->columns(2),
                        Tab::make(__('person.Notes'))
                            ->schema([
                                FormComponents\Textarea::make('notes')
                                    ->label(__('person.Notes')),
                            ]),
                    ])->columnSpanFull(),
            ])->columns(5);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->with(['occupy.building']))
            ->columns([
                TableColumns\TextColumn::make('name')
                    ->label(__('person.Name'))
                    ->searchable()
                    ->sortable(),
                TableColumns\TextColumn::make('nik')
                    ->label(__('person.NIK'))
                    ->fontFamily(FontFamily::Mono)
                    ->toggleable(),
                TableColumns\TextColumn::make('occupy.building.label')
                    ->label(__('person.Residence'))
                    ->toggleable()
                    ->sortable(),
                TableColumns\TextColumn::make('created_at')
                    ->hidden()
                    ->dateTime(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => ListPeople::route('/'),
            'create' => CreatePerson::route('/create'),
            'edit' => EditPerson::route('/{record}/edit'),
        ];
    }
}
