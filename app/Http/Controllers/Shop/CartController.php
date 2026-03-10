<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(
        private readonly CartService $cartService,
    ) {}

    public function index(): View
    {
        $cartData = $this->cartService->getItemsWithProducts();

        return view('shop.cart', [
            'items' => $cartData['items'],
            'total' => $cartData['total'],
        ]);
    }

    public function add(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'qty' => ['integer', 'min:1'],
            'payment_method' => ['nullable', 'in:cash,qr,delivery'],
        ]);

        $this->cartService->addItem(
            (int) $validated['product_id'],
            (int) ($validated['qty'] ?? 1),
            $validated['payment_method'] ?? null,
        );

        if (! empty($validated['payment_method'])) {
            return redirect()->route('checkout.show');
        }

        return redirect()->route('cart.index');
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'qty' => ['required', 'integer', 'min:0'],
        ]);

        $this->cartService->updateItem(
            (int) $validated['product_id'],
            (int) $validated['qty'],
        );

        return redirect()->route('cart.index');
    }

    public function remove(int $id): RedirectResponse
    {
        $this->cartService->removeItem($id);

        return redirect()->route('cart.index');
    }
}
