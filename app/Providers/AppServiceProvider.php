<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\CartService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('layouts.shop', function ($view) {
            $cartCount = 0;
            $driverName = 'Гость';

            if (auth()->check()) {
                $cartCount = app(CartService::class)->getItemsCount();
                $driverName = auth()->user()->name;
            }

            $view->with('cartCount', $cartCount);
            $view->with('driverName', $driverName);
        });
    }
}
