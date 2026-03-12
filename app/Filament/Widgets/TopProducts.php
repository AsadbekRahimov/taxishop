<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class TopProducts extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Топ-5 товаров';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->withCount('orderItems')
                    ->orderByDesc('order_items_count')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\ImageColumn::make('main_image')
                    ->label('Фото')
                    ->disk('public')
                    ->circular(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Товар'),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Категория'),

                Tables\Columns\TextColumn::make('price')
                    ->label('Цена')
                    ->money('RUB'),

                Tables\Columns\TextColumn::make('order_items_count')
                    ->label('Заказов')
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
