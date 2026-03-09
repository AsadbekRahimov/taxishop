<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $today = now()->startOfDay();

        return [
            Stat::make('Заказов сегодня', Order::where('created_at', '>=', $today)->count())
                ->icon('heroicon-o-shopping-cart')
                ->color('primary'),

            Stat::make('Выручка сегодня', number_format(
                (float) Order::where('created_at', '>=', $today)
                    ->where('status', '!=', 'cancelled')
                    ->sum('total'),
                2, '.', ' '
            ) . ' ₽')
                ->icon('heroicon-o-banknotes')
                ->color('success'),

            Stat::make('Активных товаров', Product::where('is_active', true)->count())
                ->icon('heroicon-o-shopping-bag')
                ->color('info'),

            Stat::make('Активных водителей', User::where('role', 'driver')->where('is_active', true)->count())
                ->icon('heroicon-o-truck')
                ->color('warning'),
        ];
    }
}
