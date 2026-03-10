<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function search(Request $request): View
    {
        $q = $request->string('q')->trim()->toString();

        $products = Product::query()
            ->where('is_active', true)
            ->when($q !== '', function ($query) use ($q) {
                $query->where('name', 'LIKE', '%' . $q . '%');
            })
            ->paginate(12)
            ->withQueryString();

        $inStockProductIds = auth()->user()
            ->driverStock()
            ->pluck('product_id')
            ->toArray();

        return view('shop.search', compact('products', 'q', 'inStockProductIds'));
    }
}
