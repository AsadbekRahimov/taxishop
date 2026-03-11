@extends('layouts.shop')

@section('title', $product->name)

@section('content')
@php
    $allImages = collect();
    if ($product->main_image) {
        $allImages->push(asset('storage/' . $product->main_image));
    }
    foreach ($product->images as $img) {
        $allImages->push(asset('storage/' . $img->image_path));
    }
    if ($allImages->isEmpty()) {
        $allImages->push('https://placehold.co/600x600/F3F4F6/1B5E20?text=' . urlencode($product->name));
    }
@endphp

<div x-data="{ currentImage: 0, images: {{ $allImages->toJson() }} }">
    <div class="grid lg:grid-cols-[60%_40%] gap-8 mt-5">

        {{-- Left Column - Images --}}
        <div>
            <div class="bg-white rounded-2xl p-4 mb-4">
                <img :src="images[currentImage]"
                     alt="{{ $product->name }}"
                     class="w-full h-[400px] lg:h-[500px] object-contain rounded-xl">
            </div>

            @if($allImages->count() > 1)
                <div class="grid grid-cols-4 gap-3">
                    <template x-for="(image, index) in images" :key="index">
                        <button @click="currentImage = index"
                                class="bg-white rounded-xl p-3 hover:shadow-lg transition-all"
                                :class="{ 'ring-2 ring-primary': currentImage === index }">
                            <img :src="image"
                                 :alt="'{{ $product->name }} ' + (index + 1)"
                                 class="w-full h-20 object-contain">
                        </button>
                    </template>
                </div>
            @endif
        </div>

        {{-- Right Column - Product Info --}}
        <div>
            <x-breadcrumbs :breadcrumbs="$breadcrumbs" :current="$product->name" />

            <h1 class="text-2xl lg:text-3xl font-extrabold mb-4">{{ $product->name }}</h1>

            @if($inStock)
                <span class="inline-block bg-primary text-white text-sm font-bold px-4 py-2 rounded-full mb-4">
                    <i class="fa-solid fa-bolt mr-2"></i>{{ __('shop.in_car_qty', ['qty' => $stockQty]) }}
                </span>
            @endif

            {{-- Price --}}
            <div class="flex items-baseline gap-3 mb-6">
                <span class="text-3xl font-extrabold text-primary">{{ number_format((float) $product->price, 0, ',', ' ') }} {{ __('shop.currency') }}</span>
                @if($product->old_price)
                    <span class="text-xl text-text-muted line-through">{{ number_format((float) $product->old_price, 0, ',', ' ') }} {{ __('shop.currency') }}</span>
                    <span class="bg-accent text-white text-sm font-bold px-2 py-1 rounded-lg">
                        -{{ round(($product->old_price - $product->price) / $product->old_price * 100) }}%
                    </span>
                @endif
            </div>

            {{-- Description --}}
            @if($product->description)
                <div class="bg-bg-color rounded-xl p-5 mb-6">
                    <h3 class="font-bold text-lg mb-3">{{ __('shop.about_product') }}</h3>
                    <div class="text-text-muted leading-relaxed prose prose-sm max-w-none">
                        {!! $product->description !!}
                    </div>
                </div>
            @endif

            {{-- Action Buttons --}}
            <div class="space-y-3">
                {{-- Buy on the spot (pickup) - only if in stock --}}
                @if($inStock)
                    <form action="{{ route('cart.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="qty" value="1">
                        <input type="hidden" name="order_type" value="pickup">
                        <button type="submit"
                                class="w-full bg-primary text-white font-bold py-4 px-6 rounded-xl hover:bg-primary-light transition-all active:scale-[0.98] flex items-center justify-center gap-3">
                            <i class="fa-solid fa-hand-holding-dollar text-xl"></i>
                            <span>{{ __('shop.buy_on_spot') }}</span>
                        </button>
                    </form>
                @else
                    <div class="w-full bg-gray-200 text-text-muted font-bold py-4 px-6 rounded-xl flex items-center justify-center gap-3 cursor-not-allowed">
                        <i class="fa-solid fa-hand-holding-dollar text-xl"></i>
                        <span>{{ __('shop.not_in_car') }}</span>
                    </div>
                @endif

                {{-- Order delivery --}}
                <form action="{{ route('cart.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="qty" value="1">
                    <input type="hidden" name="order_type" value="delivery">
                    <button type="submit"
                            class="w-full bg-accent text-white font-bold py-4 px-6 rounded-xl hover:bg-accent/90 transition-all active:scale-[0.98] flex items-center justify-center gap-3">
                        <i class="fa-solid fa-truck text-xl"></i>
                        <span>{{ __('shop.order_delivery') }}</span>
                    </button>
                </form>
            </div>

            {{-- Info Note --}}
            <div class="mt-6 p-4 bg-blue/10 rounded-xl">
                <p class="text-sm text-blue flex items-start gap-2">
                    <i class="fa-solid fa-info-circle mt-0.5"></i>
                    <span>{{ __('shop.driver_confirmation_note') }}</span>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
