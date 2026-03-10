<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function show(Request $request, string $slug): View
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $query = $category->products()->where('is_active', true);

        $sort = $request->get('sort', 'popular');

        $query = match ($sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'new' => $query->orderBy('created_at', 'desc'),
            default => $query->withCount('orderItems')->orderByDesc('order_items_count'),
        };

        $products = $query->paginate(12)->withQueryString();

        $breadcrumbs = $this->buildBreadcrumbs($category);

        $inStockProductIds = auth()->user()
            ->driverStock()
            ->pluck('product_id')
            ->toArray();

        return view('shop.category', compact(
            'category',
            'products',
            'breadcrumbs',
            'inStockProductIds',
            'sort',
        ));
    }

    private function buildBreadcrumbs(Category $category): array
    {
        $breadcrumbs = [];
        $current = $category;

        while ($current) {
            array_unshift($breadcrumbs, $current);
            $current = $current->parent;
        }

        return $breadcrumbs;
    }
}
