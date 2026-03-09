<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrders extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Последние заказы';

    public function table(Table $table): Table
    {
        return $table
            ->query(Order::query()->with('driver')->latest()->limit(10))
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Номер'),

                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Клиент'),

                Tables\Columns\TextColumn::make('driver.name')
                    ->label('Водитель'),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Оплата')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'cash' => 'success',
                        'qr' => 'info',
                        'delivery' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'cash' => 'Наличные',
                        'qr' => 'QR-код',
                        'delivery' => 'При доставке',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'gray',
                        'paid' => 'success',
                        'delivered' => 'primary',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'new' => 'Новый',
                        'paid' => 'Оплачен',
                        'delivered' => 'Доставлен',
                        'cancelled' => 'Отменён',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('total')
                    ->label('Сумма')
                    ->money('RUB'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата')
                    ->dateTime('d.m.Y H:i'),
            ])
            ->paginated(false);
    }
}
