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
                $terms = array_filter(explode(' ', mb_strtolower($q)));
                
                $query->where(function ($query) use ($terms) {
                    foreach ($terms as $term) {
                        $query->where(function ($subQuery) use ($term) {
                            $subQuery->whereRaw('LOWER(name) LIKE ?', ['%' . $term . '%'])
                                     ->orWhereRaw('LOWER(description) LIKE ?', ['%' . $term . '%']);
                        });
                    }
                });
            })
            ->paginate(12)
            ->withQueryString();

        $inStockProductIds = auth()->check()
            ? auth()->user()
                ->driverStock()
                ->pluck('product_id')
                ->toArray()
            : [];

        return view('shop.search', compact('products', 'q', 'inStockProductIds'));
    }
}
