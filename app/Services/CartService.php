<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

class CartService
{
    public function getCart(): array
    {
        return session('cart', []);
    }

    public function addItem(int $productId, int $qty = 1, ?string $paymentMethod = null): void
    {
        $cart = $this->getCart();

        if (isset($cart[$productId])) {
            $cart[$productId]['qty'] += $qty;
        } else {
            $cart[$productId] = [
                'qty' => $qty,
                'payment_method' => $paymentMethod,
            ];
        }

        if ($paymentMethod !== null) {
            $cart[$productId]['payment_method'] = $paymentMethod;
        }

        session(['cart' => $cart]);
    }

    public function updateItem(int $productId, int $qty): void
    {
        $cart = $this->getCart();

        if ($qty <= 0) {
            unset($cart[$productId]);
        } elseif (isset($cart[$productId])) {
            $cart[$productId]['qty'] = $qty;
        }

        session(['cart' => $cart]);
    }

    public function removeItem(int $productId): void
    {
        $cart = $this->getCart();
        unset($cart[$productId]);
        session(['cart' => $cart]);
    }

    public function getItemsWithProducts(): array
    {
        $cart = $this->getCart();

        if (empty($cart)) {
            return ['items' => [], 'products' => collect(), 'total' => 0];
        }

        $products = Product::whereIn('id', array_keys($cart))->get()->keyBy('id');

        $items = [];
        $total = 0;

        foreach ($cart as $productId => $data) {
            $product = $products->get($productId);
            if (! $product) {
                continue;
            }

            $subtotal = (float) $product->price * $data['qty'];
            $total += $subtotal;

            $items[] = [
                'product' => $product,
                'qty' => $data['qty'],
                'payment_method' => $data['payment_method'] ?? null,
                'subtotal' => $subtotal,
            ];
        }

        return [
            'items' => $items,
            'products' => $products,
            'total' => $total,
        ];
    }

    public function getTotal(): float
    {
        $cart = $this->getCart();

        if (empty($cart)) {
            return 0;
        }

        $products = Product::whereIn('id', array_keys($cart))->get()->keyBy('id');
        $total = 0;

        foreach ($cart as $productId => $data) {
            $product = $products->get($productId);
            if ($product) {
                $total += (float) $product->price * $data['qty'];
            }
        }

        return $total;
    }

    public function getItemsCount(): int
    {
        $cart = $this->getCart();

        return array_sum(array_column($cart, 'qty'));
    }

    public function clear(): void
    {
        session()->forget('cart');
    }

    public function isEmpty(): bool
    {
        return empty($this->getCart());
    }
}
