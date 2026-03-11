@extends('layouts.driver')

@section('title', __('shop.driver_orders'))

@section('content')
<div x-data="{ tab: 'pending' }">
    <h1 class="text-xl font-extrabold mb-4 flex items-center gap-2">
        <i class="fa-solid fa-clipboard-list text-primary"></i>
        {{ __('shop.driver_orders') }}
        <span class="text-sm font-normal text-text-muted">({{ auth()->user()?->car_number }})</span>
    </h1>

    {{-- Tabs --}}
    <div class="flex gap-2 mb-6">
        <button @click="tab = 'pending'"
                :class="tab === 'pending' ? 'bg-amber-500 text-white' : 'bg-white text-text-main border border-border'"
                class="flex-1 py-3 px-4 rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-2">
            <i class="fa-solid fa-clock"></i>
            {{ __('shop.pending_orders') }}
            @if($pendingOrders->count() > 0)
                <span class="bg-white text-amber-500 text-xs font-bold w-6 h-6 flex items-center justify-center rounded-full">
                    {{ $pendingOrders->count() }}
                </span>
            @endif
        </button>
        <button @click="tab = 'history'"
                :class="tab === 'history' ? 'bg-primary text-white' : 'bg-white text-text-main border border-border'"
                class="flex-1 py-3 px-4 rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-2">
            <i class="fa-solid fa-check-circle"></i>
            {{ __('shop.order_history') }}
        </button>
    </div>

    {{-- Pending Orders --}}
    <div x-show="tab === 'pending'">
        @forelse($pendingOrders as $order)
            <div class="bg-white rounded-2xl p-5 shadow-sm mb-4 border-l-4 border-amber-400">
                {{-- Header --}}
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <div class="font-bold text-lg">{{ $order->order_number }}</div>
                        <div class="text-sm text-text-muted">{{ $order->created_at->diffForHumans() }}</div>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold
                        {{ $order->isPickup() ? 'bg-green-100 text-green-700' : 'bg-blue/10 text-blue' }}">
                        {{ $order->isPickup() ? __('shop.pickup_badge') : __('shop.delivery_badge') }}
                    </span>
                </div>

                {{-- Customer info --}}
                @if($order->customer_name)
                    <div class="text-sm text-text-muted mb-2">
                        <i class="fa-solid fa-user mr-1"></i> {{ $order->customer_name }}
                        @if($order->customer_phone)
                            &middot; <i class="fa-solid fa-phone mr-1"></i> {{ $order->customer_phone }}
                        @endif
                    </div>
                @endif

                @if($order->delivery_address)
                    <div class="text-sm text-text-muted mb-2">
                        <i class="fa-solid fa-map-marker-alt mr-1"></i> {{ $order->delivery_address }}
                    </div>
                @endif

                {{-- Payment method --}}
                <div class="text-sm mb-3">
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg
                        {{ $order->payment_method === 'cash' ? 'bg-green-100 text-green-700' : ($order->payment_method === 'qr' ? 'bg-blue/10 text-blue' : 'bg-amber-100 text-amber-700') }}">
                        @if($order->payment_method === 'cash')
                            <i class="fa-solid fa-money-bill-wave"></i> {{ __('shop.cash_payment') }}
                        @elseif($order->payment_method === 'qr')
                            <i class="fa-solid fa-qrcode"></i> {{ __('shop.qr_code') }}
                        @else
                            <i class="fa-solid fa-clock"></i>
                            {{ $order->isPickup() ? __('shop.pay_on_receipt') : __('shop.pay_on_delivery') }}
                        @endif
                    </span>
                </div>

                {{-- Items --}}
                <div class="bg-bg-color rounded-xl p-3 mb-4">
                    @foreach($order->items as $item)
                        <div class="flex justify-between items-center py-1.5 {{ !$loop->last ? 'border-b border-border' : '' }}">
                            <div class="text-sm">
                                <span class="font-semibold">{{ $item->product->name }}</span>
                                <span class="text-text-muted">&times;{{ $item->quantity }}</span>
                            </div>
                            <span class="text-sm font-bold">{{ number_format((float) $item->subtotal, 0, ',', ' ') }} {{ __('shop.currency') }}</span>
                        </div>
                    @endforeach
                </div>

                {{-- Total --}}
                <div class="flex justify-between items-center mb-4">
                    <span class="text-text-muted">{{ __('shop.total') }}</span>
                    <span class="text-xl font-extrabold text-primary">{{ number_format((float) $order->total, 0, ',', ' ') }} {{ __('shop.currency') }}</span>
                </div>

                {{-- Actions --}}
                <div class="flex gap-3">
                    <form action="{{ route('driver.orders.confirm', $order) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit"
                                class="w-full bg-primary text-white font-bold py-3 px-4 rounded-xl hover:bg-primary-light transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                            <i class="fa-solid fa-check"></i>
                            {{ __('shop.confirm_btn') }}
                        </button>
                    </form>
                    <form action="{{ route('driver.orders.cancel', $order) }}" method="POST" class="flex-1"
                          onsubmit="return confirm('{{ __('shop.cancel_confirm') }}')">
                        @csrf
                        <button type="submit"
                                class="w-full bg-red-50 text-red-600 font-bold py-3 px-4 rounded-xl hover:bg-red-100 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                            <i class="fa-solid fa-times"></i>
                            {{ __('shop.cancel_btn') }}
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center py-16">
                <i class="fa-solid fa-check-circle text-5xl text-green-400 mb-4"></i>
                <p class="text-xl text-text-muted">{{ __('shop.no_pending_orders') }}</p>
            </div>
        @endforelse
    </div>

    {{-- History --}}
    <div x-show="tab === 'history'">
        @forelse($confirmedOrders as $order)
            <div class="bg-white rounded-2xl p-5 shadow-sm mb-4 border-l-4
                {{ $order->status === 'confirmed' ? 'border-green-400' : ($order->status === 'delivered' ? 'border-blue' : 'border-red-400') }}">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <div class="font-bold">{{ $order->order_number }}</div>
                        <div class="text-xs text-text-muted">{{ $order->created_at->format('d.m.Y H:i') }}</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-1 rounded-full text-xs font-bold
                            {{ $order->isPickup() ? 'bg-green-100 text-green-700' : 'bg-blue/10 text-blue' }}">
                            {{ $order->isPickup() ? __('shop.pickup_badge') : __('shop.delivery_badge') }}
                        </span>
                        <span class="px-2 py-1 rounded-full text-xs font-bold
                            {{ $order->status === 'confirmed' ? 'bg-green-100 text-green-700' : ($order->status === 'delivered' ? 'bg-blue/10 text-blue' : 'bg-red-100 text-red-700') }}">
                            @if($order->status === 'confirmed')
                                {{ __('shop.status_confirmed') }}
                            @elseif($order->status === 'delivered')
                                {{ __('shop.status_delivered') }}
                            @else
                                {{ __('shop.status_cancelled') }}
                            @endif
                        </span>
                    </div>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-text-muted">
                        {{ $order->items->count() }} {{ __('shop.items_count') }}
                    </span>
                    <span class="font-bold text-primary">{{ number_format((float) $order->total, 0, ',', ' ') }} {{ __('shop.currency') }}</span>
                </div>
            </div>
        @empty
            <div class="text-center py-16">
                <i class="fa-solid fa-clipboard-list text-5xl text-text-muted mb-4"></i>
                <p class="text-xl text-text-muted">{{ __('shop.no_orders_yet') }}</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
