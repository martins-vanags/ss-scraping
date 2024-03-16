<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CarResource\Pages;
use App\Models\Car;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\SelectConstraint;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class CarResource extends Resource
{
    protected static ?string $model = Car::class;

    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Car details')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('mark'),
                        Forms\Components\TextInput::make('model'),
                        Forms\Components\TextInput::make('year'),
                        Forms\Components\TextInput::make('color'),
                        Forms\Components\TextInput::make('fuel_type'),
                        Forms\Components\TextInput::make('motor'),
                        Forms\Components\TextInput::make('gearbox'),
                        Forms\Components\TextInput::make('body_type'),
                        Forms\Components\TextInput::make('mileage_in_km'),
                        Forms\Components\TextInput::make('technical_inspection_date'),
                        Forms\Components\TextInput::make('price')
                            ->label('Price')
                            ->suffix('€')
                            ->columnSpanFull(),
                        Forms\Components\TagsInput::make('specifications')
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Car link details')
                    ->columns(1)
                    ->schema([
                        Forms\Components\TextInput::make('reference_url')
                            ->label('Reference URL')
                            ->url()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('mark'),
                Tables\Columns\TextColumn::make('model'),
                Tables\Columns\TextColumn::make('year'),
                Tables\Columns\TextColumn::make('motor'),
                Tables\Columns\TextColumn::make('fuel_type'),
                Tables\Columns\TextColumn::make('gearbox'),
                Tables\Columns\TextColumn::make('color')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('body_type')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('mileage_in_km'),
                Tables\Columns\TextColumn::make('technical_inspection_date')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('price')
                    ->sortable()
                    ->label('Price'),
                Tables\Columns\TextColumn::make('upload_date')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                QueryBuilder::make()
                    ->constraints([
                        NumberConstraint::make('price')
                            ->label('Price')
                            ->icon('heroicon-o-currency-euro'),
                        SelectConstraint::make('mark')
                            ->multiple()
                            ->icon('heroicon-o-wrench')
                            ->options(
                                Car::distinct()->pluck('mark')->mapWithKeys(fn($item) => [$item => $item])->toArray(),
                            ),
                        SelectConstraint::make('model')
                            ->multiple()
                            ->icon('heroicon-o-wrench')
                            ->options(
                                Car::distinct()->pluck('model')->mapWithKeys(fn($item) => [$item => $item])->toArray(),
                            ),
                        SelectConstraint::make('year')
                            ->multiple()
                            ->icon('heroicon-o-calendar')
                            ->options(
                                Car::distinct()->pluck('year')->mapWithKeys(fn($item) => [$item => $item])->toArray(),
                            ),
                        SelectConstraint::make('motor')
                            ->multiple()
                            ->icon('heroicon-o-wrench')
                            ->options(
                                Car::distinct()->pluck('motor')->mapWithKeys(fn($item) => [$item => $item])->toArray(),
                            ),
                        SelectConstraint::make('fuel_type')
                            ->multiple()
                            ->icon('heroicon-o-wrench')
                            ->options(
                                Car::distinct()->pluck('fuel_type')->mapWithKeys(fn($item) => [$item => $item])->toArray(),
                            ),
                        SelectConstraint::make('gearbox')
                            ->multiple()
                            ->icon('heroicon-o-wrench')
                            ->options(
                                Car::distinct()->pluck('gearbox')->mapWithKeys(fn($item) => [$item => $item])->toArray(),
                            ),
                        SelectConstraint::make('color')
                            ->multiple()
                            ->icon('heroicon-o-paint-brush')
                            ->options(
                                Car::distinct()->pluck('color')->mapWithKeys(fn($item) => [$item => $item])->toArray(),
                            ),
                        SelectConstraint::make('body_type')
                            ->multiple()
                            ->options(
                                Car::distinct()->pluck('body_type')->mapWithKeys(fn($item) => [$item => $item])->toArray(),
                            ),
                        DateConstraint::make('upload_date')
                            ->label('Uploaded at')
                            ->icon('heroicon-o-calendar'),
                    ])
                    ->constraintPickerColumns(2),
            ], layout: Tables\Enums\FiltersLayout::AboveContentCollapsible)
            ->deferFilters()
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        $images = [];

        foreach($infolist->record->images as $image) {
            $images[] = ImageEntry::make('car_images')
                ->label('')
                ->url(url($image))
                ->openUrlInNewTab()
                ->defaultImageUrl(url($image));
        }

        return $infolist->schema([
            Section::make()
                ->columns(3)
                ->schema([
                    Group::make()
                        ->columns(2)
                        ->columnSpanFull()
                        ->schema([
                            TextEntry::make('mark')
                                ->icon('heroicon-o-information-circle'),
                            TextEntry::make('model')
                                ->icon('heroicon-o-information-circle'),
                            TextEntry::make('color')
                                ->icon('heroicon-o-information-circle'),
                            TextEntry::make('year')
                                ->icon('heroicon-o-information-circle'),
                            TextEntry::make('fuel_type')
                                ->icon('heroicon-o-information-circle'),
                            TextEntry::make('motor')
                                ->icon('heroicon-o-information-circle'),
                            TextEntry::make('body_type')
                                ->icon('heroicon-o-information-circle'),
                            TextEntry::make('gearbox')
                                ->icon('heroicon-o-information-circle'),
                            TextEntry::make('body_type')
                                ->icon('heroicon-o-information-circle'),
                            TextEntry::make('mileage_in_km')
                                ->icon('heroicon-o-information-circle'),
                        ]),
                ]),
            Section::make()
                ->columns(4)
                ->schema([
                    Group::make()
                        ->columns(2)
                        ->columnSpanFull()
                        ->schema([
                            TextEntry::make('technical_inspection_date')
                                ->icon('heroicon-o-calendar'),
                            TextEntry::make('price')
                                ->icon('heroicon-o-currency-euro'),
                            TextEntry::make('upload_date')
                                ->icon('heroicon-o-calendar'),
                            TextEntry::make('reference_url')
                                ->icon('heroicon-o-link')
                                ->copyable(),
                        ]),
                ]),
            Section::make('Images')
                ->columns(2)
                ->schema([
                    Group::make()
                        ->columns(4)
                        ->columnSpanFull()
                        ->schema($images),
                ])
        ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCars::route('/'),
            'view' => Pages\ViewCar::route('/{record}'),
        ];
    }
}
