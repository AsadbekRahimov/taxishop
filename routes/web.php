<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Driver\OrderController as DriverOrderController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\CategoryController;
use App\Http\Controllers\Shop\CheckoutController;
use App\Http\Controllers\Shop\HomeController;
use App\Http\Controllers\Shop\ProductController;
use App\Http\Controllers\Shop\SearchController;
use Illuminate\Support\Facades\Route;

// Locale
Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

// Auth
Route::get('/auth/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/auth/login', [LoginController::class, 'login'])->middleware('throttle:5,1');
Route::post('/auth/logout', [LoginController::class, 'logout'])->name('logout');

// Shop (requires authenticated driver)
Route::middleware(['auth', 'driver'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/category/{slug}', [CategoryController::class, 'show'])->name('category.show');
    Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.show');
    Route::get('/search', [SearchController::class, 'search'])->name('search');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');

    Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/order/{number}/thanks', [CheckoutController::class, 'thanks'])->name('order.thanks');

    // Driver panel (for driver's phone — confirm/cancel orders)
    Route::get('/driver/orders', [DriverOrderController::class, 'index'])->name('driver.orders');
    Route::post('/driver/orders/{order}/confirm', [DriverOrderController::class, 'confirm'])->name('driver.orders.confirm');
    Route::post('/driver/orders/{order}/cancel', [DriverOrderController::class, 'cancel'])->name('driver.orders.cancel');
});
