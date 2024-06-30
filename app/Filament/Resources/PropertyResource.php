<?php

namespace App\Filament\Resources;

use App\Enums\AreaAttributes\Level as AreaLevel;
use App\Filament\Resources\PropertyResource\Pages;
use App\Filament\Resources\PropertyResource\RelationManagers;
use App\Filament\Resources\PropertyResource\RelationManagers\OccupantsRelationManager;
use App\Models\Area;
use App\Models\Cluster;
use App\Models\SubRegion;
use App\Models\Property;
use Filament\Forms;
use Filament\Forms\Components as FormComponents;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static ?string $navigationIcon = 'gmdi-home-work-o';

    protected static ?string $recordTitleAttribute = 'label';

    public static function getModelLabel(): string
    {
        return __('property.resource_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FormComponents\Select::make('sub_region_id')
                    ->label(__('property.Sub_Region'))
                    ->options(function () {
                        return SubRegion::with('parent')->rtOnly()->get()
                            ->groupBy('parent.name')
                            ->transform(fn ($rows) => $rows->pluck('name', 'id'));
                    })
                    ->native(false)
                    ->required(),
                FormComponents\TextInput::make('label')
                    ->label(__('property.Label'))
                    ->required(),
                FormComponents\Select::make('cluster')
                    ->label(__('property.Cluster'))
                    ->relationship('cluster', 'name')
                    ->native(false)
                    ->columnSpan(2),
                FormComponents\Select::make('owner')
                    ->label(__('property.Owner'))
                    ->relationship('owner', 'name')
                    ->searchable()
                    ->native(false)
                    ->createOptionForm([
                        FormComponents\TextInput::make('name')
                            ->label(__('person.Name')),
                    ])
                    ->columnSpan(2),
            ])->columns(4);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')
                    ->label(__('property.Label'))
                    ->description(fn (Property $p) => $p->cluster?->name)
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('owner.name')
                    ->url(
                        fn (Property $p): ?string => $p->owner
                            ? PersonResource::getUrl('edit', ['record' => $p->owner])
                            : null
                    )
                    ->label(__('property.Owner'))
                    ->icon('gmdi-account-box-r')
                    ->toggleable(),
                TextColumn::make('subRegion.name')
                    ->label(__('property.Sub_Region'))
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('subRegion')
                    ->label(__('property.Sub_Region'))
                    ->relationship('subRegion', 'name')
                    ->native(false),
                SelectFilter::make('cluster')
                    ->label(__('property.Cluster'))
                    ->relationship('cluster', 'name')
                    ->native(false),
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
            OccupantsRelationManager::class,
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
