<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\DriverResource\Pages;
use App\Filament\Resources\DriverResource\RelationManagers;
use BackedEnum;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DriverResource extends Resource
{
    protected static ?string $model = User::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationLabel = 'Водители';

    protected static ?string $modelLabel = 'водителя';

    protected static ?string $pluralModelLabel = 'Водители';

    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'drivers';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('role', 'driver');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Section::make('Основное')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Имя')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('login')
                            ->label('Логин')
                            ->required()
                            ->maxLength(100)
                            ->unique(ignoreRecord: true)
                            ->alphaDash(),

                        Forms\Components\TextInput::make('password')
                            ->label('Пароль')
                            ->password()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->visibleOn('create'),

                        Forms\Components\TextInput::make('phone')
                            ->label('Телефон')
                            ->tel()
                            ->maxLength(20)
                            ->nullable(),

                        Forms\Components\TextInput::make('car_number')
                            ->label('Номер машины')
                            ->maxLength(20)
                            ->nullable(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Активный')
                            ->default(true),

                        Forms\Components\Hidden::make('role')
                            ->default('driver'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Имя')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('login')
                    ->label('Логин')
                    ->searchable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Телефон')
                    ->searchable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('car_number')
                    ->label('Номер машины')
                    ->searchable()
                    ->placeholder('—'),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Активный'),

                Tables\Columns\TextColumn::make('driver_stock_count')
                    ->label('Товаров на складе')
                    ->counts('driverStock')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создан')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Активные'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            RelationManagers\StockRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDrivers::route('/'),
            'create' => Pages\CreateDriver::route('/create'),
            'edit' => Pages\EditDriver::route('/{record}/edit'),
        ];
    }
}
