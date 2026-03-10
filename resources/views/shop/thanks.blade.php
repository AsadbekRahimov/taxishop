@extends('layouts.shop')

@section('title', 'Заказ оформлен')

@section('content')
<div x-data="{ showAnimation: false }"
     x-init="showAnimation = true"
     class="max-w-2xl mx-auto text-center py-12">

    {{-- Success Animation --}}
    <div class="mb-8 fade-in-up" x-show="showAnimation" x-transition:enter="transition ease-out duration-500">
        <div class="w-32 h-32 mx-auto bg-green-100 rounded-full flex items-center justify-center">
            <svg class="w-16 h-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path class="checkmark-animate" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
    </div>

    <h1 class="text-4xl font-extrabold text-primary mb-4 fade-in-up"
        x-show="showAnimation"
        x-transition:enter="transition ease-out duration-500"
        x-transition:enter-delay="100ms">
        Заказ оформлен!
    </h1>

    <p class="text-xl text-text-muted mb-8 fade-in-up"
       x-show="showAnimation"
       x-transition:enter="transition ease-out duration-500"
       x-transition:enter-delay="200ms">
        Спасибо за ваш заказ. Мы уже начали его обработку.
    </p>

    {{-- Order Details Card --}}
    <div class="bg-white rounded-2xl p-8 shadow-sm mb-8 fade-in-up text-left"
         x-show="showAnimation"
         x-transition:enter="transition ease-out duration-500"
         x-transition:enter-delay="300ms">

        <div class="space-y-4">
            <div class="flex justify-between items-center pb-4 border-b border-border">
                <span class="text-text-muted">Номер заказа:</span>
                <span class="font-bold text-lg">{{ $order->order_number }}</span>
            </div>

            <div class="flex justify-between items-center pb-4 border-b border-border">
                <span class="text-text-muted">Клиент:</span>
                <span class="font-bold">{{ $order->customer_name }}</span>
            </div>

            <div class="flex justify-between items-center pb-4 border-b border-border">
                <span class="text-text-muted">Способ оплаты:</span>
                <span class="font-bold flex items-center gap-2">
                    @switch($order->payment_method)
                        @case('cash')
                            <i class="fa-solid fa-money-bill-wave text-primary"></i> Наличные водителю
                            @break
                        @case('qr')
                            <i class="fa-solid fa-qrcode text-blue"></i> QR-код (Paynet)
                            @break
                        @case('delivery')
                            <i class="fa-solid fa-truck text-accent"></i> Оплата при доставке
                            @break
                    @endswitch
                </span>
            </div>

            @if($order->delivery_address)
                <div class="flex justify-between items-center pb-4 border-b border-border">
                    <span class="text-text-muted">Адрес доставки:</span>
                    <span class="font-bold text-right max-w-[60%]">{{ $order->delivery_address }}</span>
                </div>
            @endif

            <div class="flex justify-between items-center">
                <span class="text-text-muted">Сумма заказа:</span>
                <span class="font-extrabold text-2xl text-primary">{{ number_format((float) $order->total, 0, ',', ' ') }} сум</span>
            </div>
        </div>
    </div>

    {{-- Next Steps --}}
    <div class="bg-blue/10 rounded-2xl p-6 mb-8 fade-in-up text-left"
         x-show="showAnimation"
         x-transition:enter="transition ease-out duration-500"
         x-transition:enter-delay="400ms">
        <h3 class="font-bold text-lg mb-3 text-blue">Что дальше?</h3>
        <div class="space-y-2 text-sm text-blue">
            @switch($order->payment_method)
                @case('cash')
                    <div class="flex items-start gap-2">
                        <i class="fa-solid fa-check-circle mt-0.5"></i>
                        <span>Передайте оплату водителю наличными</span>
                    </div>
                    @break
                @case('qr')
                    <div class="flex items-start gap-2">
                        <i class="fa-solid fa-check-circle mt-0.5"></i>
                        <span>Отсканируйте QR-код в приложении для оплаты</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <i class="fa-solid fa-check-circle mt-0.5"></i>
                        <span>После оплаты водитель передаст заказ</span>
                    </div>
                    @break
                @case('delivery')
                    <div class="flex items-start gap-2">
                        <i class="fa-solid fa-check-circle mt-0.5"></i>
                        <span>Курьер доставит заказ в течение 30-60 минут</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <i class="fa-solid fa-check-circle mt-0.5"></i>
                        <span>Оплата при получении заказа</span>
                    </div>
                    @break
            @endswitch
        </div>
    </div>

    {{-- Actions --}}
    <div class="space-y-3 fade-in-up"
         x-show="showAnimation"
         x-transition:enter="transition ease-out duration-500"
         x-transition:enter-delay="500ms">

        <a href="{{ route('home') }}"
           class="inline-flex items-center justify-center gap-3 bg-primary text-white font-bold py-4 px-8 rounded-xl hover:bg-primary-light transition-all active:scale-[0.98] text-lg">
            <i class="fa-solid fa-home"></i>
            Вернуться в каталог
        </a>

        <div class="text-sm text-text-muted mt-4">
            <p>Нужна помощь? Звоните: <a href="tel:+998901234567" class="text-primary font-semibold">+998 90 123-45-67</a></p>
        </div>
    </div>
</div>
@endsection
