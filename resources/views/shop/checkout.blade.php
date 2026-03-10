@extends('layouts.shop')

@section('title', __('shop.checkout_title'))

@section('content')
<div x-data="{
    paymentMethod: '{{ old('payment_method', 'cash') }}',
}">
    <h1 class="text-3xl font-extrabold mb-8">{{ __('shop.checkout_title') }}</h1>

    <form action="{{ route('checkout.store') }}" method="POST" class="grid lg:grid-cols-[60%_40%] gap-8">
        @csrf

        {{-- Left Column - Form --}}
        <div class="space-y-6">
            {{-- Name --}}
            <div>
                <label for="customer_name" class="block text-sm font-semibold text-text-muted mb-2">{{ __('shop.your_name') }}</label>
                <input type="text"
                       id="customer_name"
                       name="customer_name"
                       value="{{ old('customer_name') }}"
                       class="w-full px-4 py-3 border-2 border-border rounded-xl text-lg outline-none transition-colors focus:border-primary bg-white @error('customer_name') border-red-500 @enderror"
                       placeholder="{{ __('shop.name_placeholder') }}"
                       required>
                @error('customer_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Phone --}}
            <div>
                <label for="customer_phone" class="block text-sm font-semibold text-text-muted mb-2">{{ __('shop.phone_number') }}</label>
                <input type="tel"
                       id="customer_phone"
                       name="customer_phone"
                       value="{{ old('customer_phone') }}"
                       class="w-full px-4 py-3 border-2 border-border rounded-xl text-lg outline-none transition-colors focus:border-primary bg-white @error('customer_phone') border-red-500 @enderror"
                       placeholder="+998901234567"
                       maxlength="13"
                       required>
                @error('customer_phone')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Payment Methods --}}
            <div>
                <label class="block text-lg font-semibold text-text-main mb-4">{{ __('shop.payment_method') }}</label>
                <div class="space-y-3">
                    {{-- Cash --}}
                    <label class="block bg-white border-2 border-border rounded-xl p-4 cursor-pointer hover:border-primary transition-all"
                           :class="{ 'border-primary bg-primary/5': paymentMethod === 'cash' }">
                        <div class="flex items-center gap-4">
                            <input type="radio" name="payment_method" value="cash"
                                   x-model="paymentMethod"
                                   class="w-5 h-5 accent-primary">
                            <div class="flex-1">
                                <div class="font-bold text-lg flex items-center gap-2">
                                    <i class="fa-solid fa-money-bill-wave text-primary"></i>
                                    {{ __('shop.cash_to_driver') }}
                                </div>
                                <p class="text-sm text-text-muted mt-1">{{ __('shop.cash_description') }}</p>
                            </div>
                        </div>
                    </label>

                    {{-- QR --}}
                    <label class="block bg-white border-2 border-border rounded-xl p-4 cursor-pointer hover:border-primary transition-all"
                           :class="{ 'border-primary bg-primary/5': paymentMethod === 'qr' }">
                        <div class="flex items-center gap-4">
                            <input type="radio" name="payment_method" value="qr"
                                   x-model="paymentMethod"
                                   class="w-5 h-5 accent-primary">
                            <div class="flex-1">
                                <div class="font-bold text-lg flex items-center gap-2">
                                    <i class="fa-solid fa-qrcode text-blue"></i>
                                    {{ __('shop.qr_code') }}
                                </div>
                                <p class="text-sm text-text-muted mt-1">{{ __('shop.qr_description') }}</p>
                            </div>
                        </div>
                    </label>

                    {{-- Delivery --}}
                    <label class="block bg-white border-2 border-border rounded-xl p-4 cursor-pointer hover:border-primary transition-all"
                           :class="{ 'border-primary bg-primary/5': paymentMethod === 'delivery' }">
                        <div class="flex items-center gap-4">
                            <input type="radio" name="payment_method" value="delivery"
                                   x-model="paymentMethod"
                                   class="w-5 h-5 accent-primary">
                            <div class="flex-1">
                                <div class="font-bold text-lg flex items-center gap-2">
                                    <i class="fa-solid fa-truck text-accent"></i>
                                    {{ __('shop.order_to_home') }}
                                </div>
                                <p class="text-sm text-text-muted mt-1">{{ __('shop.delivery_description') }}</p>
                            </div>
                        </div>
                    </label>
                </div>
                @error('payment_method')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Delivery Address (conditional) --}}
            <div x-show="paymentMethod === 'delivery'"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform -translate-y-4"
                 x-transition:enter-end="opacity-100 transform translate-y-0">
                <label for="delivery_address" class="block text-sm font-semibold text-text-muted mb-2">{{ __('shop.delivery_address') }}</label>
                <textarea id="delivery_address"
                          name="delivery_address"
                          rows="3"
                          class="w-full px-4 py-3 border-2 border-border rounded-xl text-lg outline-none transition-colors focus:border-primary bg-white resize-none @error('delivery_address') border-red-500 @enderror"
                          placeholder="{{ __('shop.address_placeholder') }}">{{ old('delivery_address') }}</textarea>
                @error('delivery_address')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Right Column - Order Summary --}}
        <div class="lg:sticky lg:top-24 h-fit">
            <div class="bg-white rounded-2xl p-6 shadow-sm">
                <h3 class="text-xl font-bold mb-5">{{ __('shop.your_order') }}</h3>

                <div class="space-y-3 mb-5">
                    @foreach($items as $item)
                        <div class="flex justify-between items-center pb-3 border-b border-border last:border-0">
                            <div>
                                <div class="font-semibold">{{ $item['product']->name }}</div>
                                <div class="text-sm text-text-muted">x{{ $item['qty'] }}</div>
                            </div>
                            <div class="font-bold text-primary">{{ number_format($item['subtotal'], 0, ',', ' ') }} {{ __('shop.currency') }}</div>
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-between items-center pt-5 border-t-2 border-border">
                    <span class="text-xl text-text-muted">{{ __('shop.total_to_pay') }}</span>
                    <span class="text-3xl font-extrabold text-primary">{{ number_format($total, 0, ',', ' ') }} {{ __('shop.currency') }}</span>
                </div>

                <button type="submit"
                        class="w-full bg-primary text-white font-bold py-4 px-6 rounded-xl hover:bg-primary-light transition-all active:scale-[0.98] flex items-center justify-center gap-3 text-lg mt-6">
                    <i class="fa-solid fa-check"></i>
                    {{ __('shop.confirm_order') }}
                </button>

                <a href="{{ route('cart.index') }}"
                   class="w-full border-2 border-border bg-white text-text-main font-bold py-3 px-6 rounded-xl hover:border-primary transition-all flex items-center justify-center gap-2 mt-3">
                    <i class="fa-solid fa-arrow-left"></i>
                    {{ __('shop.back_to_cart') }}
                </a>
            </div>
        </div>
    </form>
</div>
@endsection
