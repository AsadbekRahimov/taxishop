@extends('layouts.shop')

@section('title', __('shop.cart'))

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-extrabold mb-6 flex items-center gap-3">
        <i class="fa-solid fa-shopping-cart text-primary"></i>
        {{ __('shop.cart') }}
        @if(count($items) > 0)
            <span class="text-lg font-normal text-text-muted">
                ({{ count($items) }} {{ count($items) === 1 ? __('shop.item_one') : (count($items) < 5 ? __('shop.item_few') : __('shop.item_many')) }})
            </span>
        @endif
    </h1>

    @if(count($items) > 0)
        {{-- Cart Items --}}
        <div class="space-y-4 mb-8">
            @foreach($items as $item)
                <div class="bg-white rounded-2xl p-5 shadow-sm hover:shadow-md transition-all">
                    <div class="flex items-center gap-4">
                        {{-- Product Image --}}
                        <a href="{{ route('product.show', $item['product']->slug) }}">
                            <img src="{{ $item['product']->main_image ? asset('storage/' . $item['product']->main_image) : 'https://placehold.co/100x100/F3F4F6/1B5E20?text=' . urlencode($item['product']->name) }}"
                                 alt="{{ $item['product']->name }}"
                                 class="w-20 h-20 object-cover rounded-xl">
                        </a>

                        {{-- Product Info --}}
                        <div class="flex-1">
                            <a href="{{ route('product.show', $item['product']->slug) }}">
                                <h3 class="font-bold text-lg mb-1">{{ $item['product']->name }}</h3>
                            </a>
                            <div class="flex items-baseline gap-2">
                                <span class="text-primary font-bold">{{ number_format((float) $item['product']->price, 0, ',', ' ') }} {{ __('shop.currency') }}</span>
                                @if($item['product']->old_price)
                                    <span class="text-sm text-text-muted line-through">{{ number_format((float) $item['product']->old_price, 0, ',', ' ') }} {{ __('shop.currency') }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- Quantity Controls --}}
                        <div class="flex items-center gap-2 bg-bg-color rounded-xl p-1">
                            @if($item['qty'] > 1)
                                <form action="{{ route('cart.update') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $item['product']->id }}">
                                    <input type="hidden" name="qty" value="{{ $item['qty'] - 1 }}">
                                    <button type="submit"
                                            class="w-8 h-8 rounded-lg bg-white flex items-center justify-center hover:bg-primary hover:text-white transition-colors">
                                        <i class="fa-solid fa-minus text-xs"></i>
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('cart.remove', $item['product']->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="w-8 h-8 rounded-lg bg-white flex items-center justify-center hover:bg-red-500 hover:text-white transition-colors">
                                        <i class="fa-solid fa-minus text-xs"></i>
                                    </button>
                                </form>
                            @endif

                            <span class="w-12 text-center font-bold">{{ $item['qty'] }}</span>

                            <form action="{{ route('cart.update') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $item['product']->id }}">
                                <input type="hidden" name="qty" value="{{ $item['qty'] + 1 }}">
                                <button type="submit"
                                        class="w-8 h-8 rounded-lg bg-white flex items-center justify-center hover:bg-primary hover:text-white transition-colors">
                                    <i class="fa-solid fa-plus text-xs"></i>
                                </button>
                            </form>
                        </div>

                        {{-- Item Total --}}
                        <div class="text-right min-w-[100px]">
                            <div class="text-lg font-bold text-primary">{{ number_format($item['subtotal'], 0, ',', ' ') }} {{ __('shop.currency') }}</div>
                        </div>

                        {{-- Remove Button --}}
                        <form action="{{ route('cart.remove', $item['product']->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-10 h-10 rounded-xl bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-100 transition-colors">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Cart Summary --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm sticky bottom-5">
            <div class="flex justify-between items-center mb-6">
                <span class="text-xl text-text-muted">{{ __('shop.total') }}</span>
                <span class="text-3xl font-extrabold text-primary">{{ number_format($total, 0, ',', ' ') }} {{ __('shop.currency') }}</span>
            </div>

            <div class="space-y-3">
                <a href="{{ route('checkout.show') }}"
                   class="w-full bg-primary text-white font-bold py-4 px-6 rounded-xl hover:bg-primary-light transition-all active:scale-[0.98] flex items-center justify-center gap-3 text-lg">
                    <i class="fa-solid fa-credit-card"></i>
                    {{ __('shop.checkout') }}
                </a>

                <a href="{{ route('home') }}"
                   class="w-full border-2 border-border bg-white text-text-main font-bold py-4 px-6 rounded-xl hover:border-primary transition-all flex items-center justify-center gap-3">
                    <i class="fa-solid fa-arrow-left"></i>
                    {{ __('shop.continue_shopping') }}
                </a>
            </div>

            <div class="mt-4 p-3 bg-bg-color rounded-xl">
                <p class="text-sm text-text-muted text-center">
                    <i class="fa-solid fa-truck mr-2"></i>
                    {{ __('shop.delivery_time_note') }}
                </p>
            </div>
        </div>

    @else
        {{-- Empty Cart --}}
        <div class="text-center py-20">
            <i class="fa-solid fa-shopping-cart text-6xl text-text-muted mb-4"></i>
            <p class="text-xl text-text-muted mb-6">{{ __('shop.cart_empty') }}</p>
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 bg-primary text-white font-bold py-3 px-6 rounded-xl hover:bg-primary-light transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
                {{ __('shop.back_to_shopping') }}
            </a>
        </div>
    @endif
</div>
@endsection
