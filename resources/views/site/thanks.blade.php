@extends('layouts.site')

@section('title', $title)

@section('content')
<div x-data="{ 
    orderNumber: '{{ $orderNumber ?? '12345' }}',
    paymentMethod: '{{ $paymentMethod ?? 'cash_to_driver' }}',
    total: '{{ Session::get('order_total', '54 000 сум') }}',
    showAnimation: false
}" 
     x-init="showAnimation = true"
     class="max-w-2xl mx-auto text-center py-12">
    
    <!-- Success Animation -->
    <div class="mb-8 fade-in-up" x-show="showAnimation" x-transition:enter="transition ease-out duration-500">
        <div class="w-32 h-32 mx-auto bg-green-100 rounded-full flex items-center justify-center">
            <svg class="w-16 h-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path class="checkmark-animate" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
    </div>
    
    <!-- Success Message -->
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
    
    <!-- Order Details Card -->
    <div class="bg-white rounded-2xl p-8 shadow-sm mb-8 fade-in-up"
         x-show="showAnimation" 
         x-transition:enter="transition ease-out duration-500"
         x-transition:enter-delay="300ms">
        
        <div class="space-y-4">
            <!-- Order Number -->
            <div class="flex justify-between items-center pb-4 border-b border-border">
                <span class="text-text-muted">Номер заказа:</span>
                <span class="font-bold text-lg" x-text="orderNumber"></span>
            </div>
            
            <!-- Payment Method -->
            <div class="flex justify-between items-center pb-4 border-b border-border">
                <span class="text-text-muted">Способ оплаты:</span>
                <span class="font-bold flex items-center gap-2">
                    <template x-if="paymentMethod === 'cash_to_driver'">
                        <i class="fa-solid fa-money-bill-wave text-primary"></i>
                        <span>Наличные водителю</span>
                    </template>
                    <template x-if="paymentMethod === 'qr_paynet'">
                        <i class="fa-solid fa-qrcode text-blue"></i>
                        <span>QR-код (Paynet)</span>
                    </template>
                    <template x-if="paymentMethod === 'delivery_cod'">
                        <i class="fa-solid fa-truck text-accent"></i>
                        <span>Оплата при доставке</span>
                    </template>
                </span>
            </div>
            
            <!-- Total -->
            <div class="flex justify-between items-center">
                <span class="text-text-muted">Сумма заказа:</span>
                <span class="font-extrabold text-2xl text-primary" x-text="total"></span>
            </div>
        </div>
    </div>
    
    <!-- Next Steps -->
    <div class="bg-blue/10 rounded-2xl p-6 mb-8 fade-in-up"
         x-show="showAnimation" 
         x-transition:enter="transition ease-out duration-500"
         x-transition:enter-delay="400ms">
        <h3 class="font-bold text-lg mb-3 text-blue">Что дальше?</h3>
        <div class="space-y-2 text-sm text-blue">
            <template x-if="paymentMethod === 'cash_to_driver'">
                <div class="flex items-start gap-2">
                    <i class="fa-solid fa-check-circle mt-0.5"></i>
                    <span>Водитель получит ваш заказ и подъедет через 5-10 минут</span>
                </div>
            </template>
            <template x-if="paymentMethod === 'qr_paynet'">
                <div class="flex items-start gap-2">
                    <i class="fa-solid fa-check-circle mt-0.5"></i>
                    <span>Отсканируйте QR-код в приложении для оплаты</span>
                </div>
                <div class="flex items-start gap-2">
                    <i class="fa-solid fa-check-circle mt-0.5"></i>
                    <span>После оплаты водитель привезет заказ через 5-10 минут</span>
                </div>
            </template>
            <template x-if="paymentMethod === 'delivery_cod'">
                <div class="flex items-start gap-2">
                    <i class="fa-solid fa-check-circle mt-0.5"></i>
                    <span>Курьер доставит заказ в течение 30-60 минут</span>
                </div>
                <div class="flex items-start gap-2">
                    <i class="fa-solid fa-check-circle mt-0.5"></i>
                    <span>Оплата при получении заказа</span>
                </div>
            </template>
        </div>
    </div>
    
    <!-- Action Buttons -->
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
