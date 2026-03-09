<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class SalesChart extends ChartWidget
{
    protected ?string $heading = 'Продажи за 7 дней';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $days = collect(range(6, 0))->map(fn (int $daysAgo) => Carbon::today()->subDays($daysAgo));

        $revenue = $days->map(fn (Carbon $date) => (float) Order::whereDate('created_at', $date)
            ->where('status', '!=', 'cancelled')
            ->sum('total'));

        return [
            'datasets' => [
                [
                    'label' => 'Выручка (₽)',
                    'data' => $revenue->values()->toArray(),
                    'borderColor' => '#22c55e',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $days->map(fn (Carbon $date) => $date->format('d.m'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
