<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\DriverStock;
use App\Models\Product;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(string $slug): View
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->with(['category.parent', 'images'])
            ->firstOrFail();

        $stock = DriverStock::where('driver_id', auth()->id())
            ->where('product_id', $product->id)
            ->first();

        $inStock = $stock !== null;
        $stockQty = $stock?->quantity ?? 0;

        $breadcrumbs = [];
        $current = $product->category;
        while ($current) {
            array_unshift($breadcrumbs, $current);
            $current = $current->parent;
        }

        return view('shop.product', compact(
            'product',
            'inStock',
            'stockQty',
            'breadcrumbs',
        ));
    }
}
