<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CarResource\Pages;
use App\Models\Car;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\SelectConstraint;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                        Forms\Components\TextInput::make('upload_date'),
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
            ->columns([
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
                Tables\Columns\TextColumn::make('price')->label('Price'),
                Tables\Columns\TextColumn::make('upload_date')
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
                            ->options(
                                Car::distinct()->pluck('mark')->mapWithKeys(fn($item) => [$item => $item])->toArray(),
                            ),
                        SelectConstraint::make('model')
                            ->multiple()
                            ->options(
                                Car::distinct()->pluck('model')->mapWithKeys(fn($item) => [$item => $item])->toArray(),
                            ),
                        SelectConstraint::make('year')
                            ->multiple()
                            ->options(
                                Car::distinct()->pluck('year')->mapWithKeys(fn($item) => [$item => $item])->toArray(),
                            ),
                        SelectConstraint::make('motor')
                            ->multiple()
                            ->options(
                                Car::distinct()->pluck('motor')->mapWithKeys(fn($item) => [$item => $item])->toArray(),
                            ),
                        SelectConstraint::make('fuel_type')
                            ->multiple()
                            ->options(
                                Car::distinct()->pluck('fuel_type')->mapWithKeys(fn($item) => [$item => $item])->toArray(),
                            ),
                        SelectConstraint::make('gearbox')
                            ->multiple()
                            ->options(
                                Car::distinct()->pluck('gearbox')->mapWithKeys(fn($item) => [$item => $item])->toArray(),
                            ),
                        SelectConstraint::make('color')
                            ->multiple()
                            ->options(
                                Car::distinct()->pluck('color')->mapWithKeys(fn($item) => [$item => $item])->toArray(),
                            ),
                        SelectConstraint::make('body_type')
                            ->multiple()
                            ->options(
                                Car::distinct()->pluck('body_type')->mapWithKeys(fn($item) => [$item => $item])->toArray(),
                            ),
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
