@extends('layouts.shop')

@section('title', __('shop.home'))

@section('content')
<div>
    {{-- Two Main Blocks: In Car / Delivery --}}
    <div class="grid grid-cols-2 gap-6 mb-10 fade-in-up">
        {{-- Block 1: Products in the car --}}
        <a href="{{ route('products.in-car') }}"
           class="relative bg-gradient-to-br from-green-600 to-green-800 rounded-2xl p-8 text-white hover:shadow-2xl transition-all hover:scale-[1.02] active:scale-[0.98] overflow-hidden group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-8 translate-x-8"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/5 rounded-full translate-y-6 -translate-x-6"></div>
            <div class="relative z-10">
                <div class="text-5xl mb-4">
                    <i class="fa-solid fa-car-side"></i>
                </div>
                <h2 class="text-2xl lg:text-3xl font-extrabold mb-2">{{ __('shop.in_car_title') }}</h2>
                <p class="text-white/80 text-sm lg:text-base mb-4">{{ __('shop.in_car_subtitle') }}</p>
                <div class="flex items-center gap-2">
                    <span class="bg-white/20 backdrop-blur-sm text-white text-sm font-bold px-4 py-1.5 rounded-full">
                        {{ __('shop.products_count', ['count' => $inStockCount]) }}
                    </span>
                    <i class="fa-solid fa-arrow-right ml-auto text-lg group-hover:translate-x-1 transition-transform"></i>
                </div>
            </div>
        </a>

        {{-- Block 2: Products for delivery --}}
        <a href="{{ route('products.delivery') }}"
           class="relative bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl p-8 text-white hover:shadow-2xl transition-all hover:scale-[1.02] active:scale-[0.98] overflow-hidden group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-8 translate-x-8"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/5 rounded-full translate-y-6 -translate-x-6"></div>
            <div class="relative z-10">
                <div class="text-5xl mb-4">
                    <i class="fa-solid fa-truck-fast"></i>
                </div>
                <h2 class="text-2xl lg:text-3xl font-extrabold mb-2">{{ __('shop.delivery_title') }}</h2>
                <p class="text-white/80 text-sm lg:text-base mb-4">{{ __('shop.delivery_subtitle') }}</p>
                <div class="flex items-center gap-2">
                    <span class="bg-white/20 backdrop-blur-sm text-white text-sm font-bold px-4 py-1.5 rounded-full">
                        {{ __('shop.products_count', ['count' => $deliveryCount]) }}
                    </span>
                    <i class="fa-solid fa-arrow-right ml-auto text-lg group-hover:translate-x-1 transition-transform"></i>
                </div>
            </div>
        </a>
    </div>

    {{-- In-Car Products Preview --}}
    @if($inStockPreview->isNotEmpty())
    <section class="mb-10">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-2xl font-extrabold flex items-center gap-2.5">
                <i class="fa-solid fa-bolt text-green-600"></i> {{ __('shop.in_car_title') }}
            </h2>
            <a href="{{ route('products.in-car') }}" class="text-primary font-semibold hover:underline flex items-center gap-1">
                {{ __('shop.view_all') }} <i class="fa-solid fa-arrow-right text-sm"></i>
            </a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($inStockPreview as $product)
                <x-product-card :product="$product" :inStock="true" />
            @endforeach
        </div>
    </section>
    @endif

    {{-- Delivery Products Preview --}}
    @if($deliveryPreview->isNotEmpty())
    <section class="mb-10">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-2xl font-extrabold flex items-center gap-2.5">
                <i class="fa-solid fa-truck text-amber-500"></i> {{ __('shop.delivery_title') }}
            </h2>
            <a href="{{ route('products.delivery') }}" class="text-primary font-semibold hover:underline flex items-center gap-1">
                {{ __('shop.view_all') }} <i class="fa-solid fa-arrow-right text-sm"></i>
            </a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($deliveryPreview as $product)
                <x-product-card :product="$product" :inStock="false" />
            @endforeach
        </div>
    </section>
    @endif

    {{-- Categories --}}
    @if($categories->isNotEmpty())
    <section class="mb-8">
        <h2 class="text-2xl font-extrabold mb-5 flex items-center gap-2.5">
            <i class="fa-solid fa-th-large text-primary"></i> {{ __('shop.categories') }}
        </h2>
        <div class="grid grid-cols-3 md:grid-cols-4 gap-4">
            @foreach($categories as $category)
                <a href="{{ route('category.show', $category->slug) }}"
                   class="bg-white rounded-2xl p-5 text-center hover:shadow-lg transition-all hover:scale-105 active:scale-95">
                    <div class="text-3xl mb-2 text-primary">
                        @if($category->icon)
                            <img src="{{ asset('storage/' . $category->icon) }}" alt="{{ $category->name }}" class="w-10 h-10 mx-auto">
                        @else
                            <i class="fa-solid fa-box"></i>
                        @endif
                    </div>
                    <div class="font-semibold text-sm">{{ $category->name }}</div>
                    <div class="text-xs text-text-muted mt-1">{{ __('shop.products_count', ['count' => $category->products_count ?? $category->products()->count()]) }}</div>
                </a>

                @foreach($category->children as $child)
                    <a href="{{ route('category.show', $child->slug) }}"
                       class="bg-white rounded-2xl p-5 text-center hover:shadow-lg transition-all hover:scale-105 active:scale-95">
                        <div class="text-3xl mb-2 text-primary">
                            @if($child->icon)
                                <img src="{{ asset('storage/' . $child->icon) }}" alt="{{ $child->name }}" class="w-10 h-10 mx-auto">
                            @else
                                <i class="fa-solid fa-box-open"></i>
                            @endif
                        </div>
                        <div class="font-semibold text-sm">{{ $child->name }}</div>
                        <div class="text-xs text-text-muted mt-1">{{ __('shop.products_count', ['count' => $child->products_count ?? $child->products()->count()]) }}</div>
                    </a>
                @endforeach
            @endforeach
        </div>
    </section>
    @endif
</div>
@endsection
