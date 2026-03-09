<?php

declare(strict_types=1);

namespace App\Filament\Resources\DriverResource\RelationManagers;

use App\Models\Product;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StockRelationManager extends RelationManager
{
    protected static string $relationship = 'driverStock';

    protected static ?string $title = 'Склад водителя';

    protected static ?string $modelLabel = 'товар на складе';

    protected static ?string $pluralModelLabel = 'Товары на складе';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label('Товар')
                    ->options(function (?int $state) {
                        $existingProductIds = $this->getOwnerRecord()
                            ->driverStock()
                            ->when($state, fn (Builder $query) => $query->where('product_id', '!=', $state))
                            ->pluck('product_id')
                            ->toArray();

                        return Product::query()
                            ->where('is_active', true)
                            ->whereNotIn('id', $existingProductIds)
                            ->pluck('name', 'id');
                    })
                    ->searchable()
                    ->required(),

                Forms\Components\TextInput::make('quantity')
                    ->label('Количество')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->default(1),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('product.main_image')
                    ->label('Фото')
                    ->circular(),

                Tables\Columns\TextColumn::make('product.name')
                    ->label('Товар')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Количество')
                    ->sortable(),
            ])
            ->defaultSort('product.name')
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
