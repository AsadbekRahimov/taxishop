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

    public function show(Request $request): View|RedirectResponse
    {
        if ($this->cartService->isEmpty()) {
            return redirect()->route('home');
        }

        $orderType = $request->query('type', 'pickup');
        $cartData = $this->cartService->getItemsWithProducts();

        return view('shop.checkout', [
            'items' => $cartData['items'],
            'total' => $cartData['total'],
            'orderType' => $orderType,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if ($this->cartService->isEmpty()) {
            return redirect()->route('home');
        }

        $orderType = $request->input('order_type', 'pickup');

        $rules = [
            'order_type' => ['required', 'in:pickup,delivery'],
            'payment_method' => ['required', 'in:cash,qr,delivery'],
        ];

        $messages = [
            'payment_method.required' => 'Выберите способ оплаты.',
        ];

        if ($orderType === 'delivery') {
            $rules['customer_name'] = ['required', 'string', 'max:255'];
            $rules['customer_phone'] = ['required', 'string', 'regex:/^\+998\d{9}$/'];
            $rules['delivery_address'] = ['required', 'string', 'max:500'];

            $messages['customer_name.required'] = 'Введите ваше имя.';
            $messages['customer_phone.required'] = 'Введите номер телефона.';
            $messages['customer_phone.regex'] = 'Неверный формат номера. Пример: +998901234567';
            $messages['delivery_address.required'] = 'Введите адрес доставки.';
        }

        $validated = $request->validate($rules, $messages);

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
