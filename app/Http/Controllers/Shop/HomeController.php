<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $categories = Category::whereNull('parent_id')
            ->with('children')
            ->orderBy('sort_order')
            ->get();

        $driverStock = auth()->user()->driverStock()->with('product')->get();
        $inStockProductIds = $driverStock->pluck('product_id')->toArray();

        $inStockProducts = Product::where('is_active', true)
            ->whereIn('id', $inStockProductIds)
            ->get();

        $hits = Product::where('is_active', true)
            ->withCount('orderItems')
            ->orderByDesc('order_items_count')
            ->take(8)
            ->get();

        return view('shop.home', compact(
            'categories',
            'driverStock',
            'inStockProductIds',
            'inStockProducts',
            'hits',
        ));
    }
}
