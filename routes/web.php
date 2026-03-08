<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiteController;

// Site routes
Route::get('/', [SiteController::class, 'index'])->name('home');
Route::get('/category/{slug?}', [SiteController::class, 'category'])->name('category');
Route::get('/product/{id?}', [SiteController::class, 'product'])->name('product');
Route::get('/cart', [SiteController::class, 'cart'])->name('cart');
Route::get('/checkout', [SiteController::class, 'checkout'])->name('checkout');
Route::get('/thanks', [SiteController::class, 'thanks'])->name('thanks');
Route::get('/login', [SiteController::class, 'login'])->name('login');

// Cart AJAX routes
Route::post('/cart/add', [SiteController::class, 'addToCart'])->name('cart.add');
Route::post('/cart/remove', [SiteController::class, 'removeFromCart'])->name('cart.remove');
Route::post('/cart/update', [SiteController::class, 'updateCart'])->name('cart.update');

// Order routes
Route::post('/order/place', [SiteController::class, 'placeOrder'])->name('order.place');
Route::get('/order/thanks/{number?}', [SiteController::class, 'orderThanks'])->name('order.thanks');
