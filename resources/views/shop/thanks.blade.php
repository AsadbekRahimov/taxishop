@extends('layouts.shop')

@section('title', __('shop.order_created'))

@section('content')
<div x-data="{ showAnimation: false }"
     x-init="showAnimation = true"
     class="max-w-2xl mx-auto text-center py-12">

    {{-- Success Animation --}}
    <div class="mb-8 fade-in-up" x-show="showAnimation" x-transition:enter="transition ease-out duration-500">
        <div class="w-32 h-32 mx-auto bg-amber-100 rounded-full flex items-center justify-center">
            <i class="fa-solid fa-clock text-amber-500 text-5xl"></i>
        </div>
    </div>

    <h1 class="text-4xl font-extrabold text-primary mb-4 fade-in-up"
        x-show="showAnimation"
        x-transition:enter="transition ease-out duration-500"
        x-transition:enter-delay="100ms">
        {{ __('shop.order_created') }}
    </h1>

    <p class="text-xl text-text-muted mb-8 fade-in-up"
       x-show="showAnimation"
       x-transition:enter="transition ease-out duration-500"
       x-transition:enter-delay="200ms">
        {{ __('shop.order_waiting_confirmation') }}
    </p>

    {{-- Order Details Card --}}
    <div class="bg-white rounded-2xl p-8 shadow-sm mb-8 fade-in-up text-left"
         x-show="showAnimation"
         x-transition:enter="transition ease-out duration-500"
         x-transition:enter-delay="300ms">

        <div class="space-y-4">
            <div class="flex justify-between items-center pb-4 border-b border-border">
                <span class="text-text-muted">{{ __('shop.order_number') }}</span>
                <span class="font-bold text-lg">{{ $order->order_number }}</span>
            </div>

            <div class="flex justify-between items-center pb-4 border-b border-border">
                <span class="text-text-muted">{{ __('shop.order_type_label') }}</span>
                <span class="font-bold flex items-center gap-2">
                    @if($order->isPickup())
                        <i class="fa-solid fa-hand-holding-dollar text-primary"></i> {{ __('shop.pickup_on_spot') }}
                    @else
                        <i class="fa-solid fa-truck text-accent"></i> {{ __('shop.delivery_to_home') }}
                    @endif
                </span>
            </div>

            @if($order->customer_name)
                <div class="flex justify-between items-center pb-4 border-b border-border">
                    <span class="text-text-muted">{{ __('shop.customer') }}</span>
                    <span class="font-bold">{{ $order->customer_name }}</span>
                </div>
            @endif

            <div class="flex justify-between items-center pb-4 border-b border-border">
                <span class="text-text-muted">{{ __('shop.payment_method_label') }}</span>
                <span class="font-bold flex items-center gap-2">
                    @switch($order->payment_method)
                        @case('cash')
                            <i class="fa-solid fa-money-bill-wave text-primary"></i> {{ __('shop.cash_payment') }}
                            @break
                        @case('qr')
                            <i class="fa-solid fa-qrcode text-blue"></i> {{ __('shop.qr_paynet') }}
                            @break
                        @case('delivery')
                            <i class="fa-solid fa-clock text-accent"></i>
                            {{ $order->isPickup() ? __('shop.pay_on_receipt') : __('shop.pay_on_delivery') }}
                            @break
                    @endswitch
                </span>
            </div>

            @if($order->delivery_address)
                <div class="flex justify-between items-center pb-4 border-b border-border">
                    <span class="text-text-muted">{{ __('shop.delivery_address_label') }}</span>
                    <span class="font-bold text-right max-w-[60%]">{{ $order->delivery_address }}</span>
                </div>
            @endif

            <div class="flex justify-between items-center">
                <span class="text-text-muted">{{ __('shop.order_total') }}</span>
                <span class="font-extrabold text-2xl text-primary">{{ number_format((float) $order->total, 0, ',', ' ') }} {{ __('shop.currency') }}</span>
            </div>
        </div>
    </div>

    {{-- Waiting for confirmation --}}
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 mb-8 fade-in-up"
         x-show="showAnimation"
         x-transition:enter="transition ease-out duration-500"
         x-transition:enter-delay="400ms">
        <div class="flex items-center justify-center gap-3 mb-3">
            <div class="animate-spin w-5 h-5 border-2 border-amber-500 border-t-transparent rounded-full"></div>
            <h3 class="font-bold text-lg text-amber-700">{{ __('shop.waiting_driver_title') }}</h3>
        </div>
        <p class="text-sm text-amber-600">
            @if($order->isPickup())
                {{ __('shop.waiting_driver_pickup') }}
            @else
                {{ __('shop.waiting_driver_delivery') }}
            @endif
        </p>
    </div>

    {{-- Actions --}}
    <div class="space-y-3 fade-in-up"
         x-show="showAnimation"
         x-transition:enter="transition ease-out duration-500"
         x-transition:enter-delay="500ms">

        <a href="{{ route('home') }}"
           class="inline-flex items-center justify-center gap-3 bg-primary text-white font-bold py-4 px-8 rounded-xl hover:bg-primary-light transition-all active:scale-[0.98] text-lg">
            <i class="fa-solid fa-home"></i>
            {{ __('shop.back_to_catalog') }}
        </a>

        <div class="text-sm text-text-muted mt-4">
            <p>{{ __('shop.need_help') }} <a href="tel:+998901234567" class="text-primary font-semibold">+998 90 123-45-67</a></p>
        </div>
    </div>
</div>
@endsection
