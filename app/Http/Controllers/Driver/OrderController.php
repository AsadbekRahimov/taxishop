<?php

declare(strict_types=1);

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
    ) {}

    public function index(): View
    {
        $pendingOrders = Order::where('driver_id', auth()->id())
            ->where('status', 'pending')
            ->with('items.product')
            ->orderByDesc('created_at')
            ->get();

        $confirmedOrders = Order::where('driver_id', auth()->id())
            ->whereIn('status', ['confirmed', 'delivered'])
            ->with('items.product')
            ->orderByDesc('created_at')
            ->take(20)
            ->get();

        return view('driver.orders', compact('pendingOrders', 'confirmedOrders'));
    }

    public function confirm(Order $order): RedirectResponse
    {
        if ($order->driver_id !== auth()->id()) {
            abort(403);
        }

        if (! $order->isPending()) {
            return redirect()->route('driver.orders')
                ->with('error', __('shop.order_already_processed'));
        }

        $this->orderService->confirmOrder($order);

        return redirect()->route('driver.orders')
            ->with('success', __('shop.order_confirmed_success', ['number' => $order->order_number]));
    }

    public function cancel(Order $order): RedirectResponse
    {
        if ($order->driver_id !== auth()->id()) {
            abort(403);
        }

        if (! $order->isPending()) {
            return redirect()->route('driver.orders')
                ->with('error', __('shop.order_already_processed'));
        }

        $this->orderService->cancelOrder($order);

        return redirect()->route('driver.orders')
            ->with('success', __('shop.order_cancelled_success', ['number' => $order->order_number]));
    }
}
