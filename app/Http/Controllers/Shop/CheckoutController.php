<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly OrderService $orderService,
    ) {}

    public function show(): View|RedirectResponse
    {
        if ($this->cartService->isEmpty()) {
            return redirect()->route('home');
        }

        $cartData = $this->cartService->getItemsWithProducts();

        return view('shop.checkout', [
            'items' => $cartData['items'],
            'total' => $cartData['total'],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if ($this->cartService->isEmpty()) {
            return redirect()->route('home');
        }

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'regex:/^\+998\d{9}$/'],
            'payment_method' => ['required', 'in:cash,qr,delivery'],
            'delivery_address' => ['required_if:payment_method,delivery', 'nullable', 'string', 'max:500'],
        ], [
            'customer_name.required' => 'Введите ваше имя.',
            'customer_phone.required' => 'Введите номер телефона.',
            'customer_phone.regex' => 'Неверный формат номера. Пример: +998901234567',
            'payment_method.required' => 'Выберите способ оплаты.',
            'delivery_address.required_if' => 'Введите адрес доставки.',
        ]);

        $cartData = $this->cartService->getItemsWithProducts();

        $order = $this->orderService->createOrder(
            $validated,
            $cartData['items'],
            auth()->id(),
        );

        $this->cartService->clear();

        return redirect()->route('order.thanks', $order->order_number);
    }

    public function thanks(string $number): View
    {
        $order = \App\Models\Order::where('order_number', $number)
            ->where('driver_id', auth()->id())
            ->with('items.product')
            ->firstOrFail();

        return view('shop.thanks', compact('order'));
    }
}
