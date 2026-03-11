<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $driverStock = auth()->user()->driverStock()->with('product')->get();
        $inStockProductIds = $driverStock->pluck('product_id')->toArray();
        $inStockCount = count($inStockProductIds);

        $deliveryCount = Product::where('is_active', true)
            ->whereNotIn('id', $inStockProductIds)
            ->count();

        $categories = Category::whereNull('parent_id')
            ->with('children')
            ->orderBy('sort_order')
            ->get();

        // Preview: 4 products for each section
        $inStockPreview = Product::where('is_active', true)
            ->whereIn('id', $inStockProductIds)
            ->take(4)
            ->get();

        $deliveryPreview = Product::where('is_active', true)
            ->whereNotIn('id', $inStockProductIds)
            ->take(4)
            ->get();

        return view('shop.home', compact(
            'categories',
            'inStockProductIds',
            'inStockCount',
            'deliveryCount',
            'inStockPreview',
            'deliveryPreview',
        ));
    }

    public function inCar(Request $request): View
    {
        $driverStock = auth()->user()->driverStock()->with('product')->get();
        $inStockProductIds = $driverStock->pluck('product_id')->toArray();
        $sort = $request->get('sort', 'popular');

        $query = Product::where('is_active', true)
            ->whereIn('id', $inStockProductIds);

        $query = $this->applySort($query, $sort);

        $products = $query->paginate(12)->appends(['sort' => $sort]);

        return view('shop.in-car', compact('products', 'inStockProductIds', 'sort'));
    }

    public function delivery(Request $request): View
    {
        $driverStock = auth()->user()->driverStock()->with('product')->get();
        $inStockProductIds = $driverStock->pluck('product_id')->toArray();
        $sort = $request->get('sort', 'popular');

        $query = Product::where('is_active', true)
            ->whereNotIn('id', $inStockProductIds);

        $query = $this->applySort($query, $sort);

        $products = $query->paginate(12)->appends(['sort' => $sort]);

        return view('shop.delivery', compact('products', 'inStockProductIds', 'sort'));
    }

    private function applySort($query, string $sort)
    {
        return match ($sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'new' => $query->orderByDesc('created_at'),
            default => $query->withCount('orderItems')->orderByDesc('order_items_count'),
        };
    }
}
