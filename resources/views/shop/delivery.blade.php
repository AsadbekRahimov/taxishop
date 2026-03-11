@extends('layouts.shop')

@section('title', __('shop.delivery_title'))

@section('content')
    <div class="flex items-center gap-3 mb-2">
        <a href="{{ route('home') }}" class="text-primary hover:underline">
            <i class="fa-solid fa-arrow-left"></i> {{ __('shop.back_to_home') }}
        </a>
    </div>

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl md:text-4xl font-extrabold flex items-center gap-3">
            <i class="fa-solid fa-truck-fast text-amber-500"></i> {{ __('shop.delivery_title') }}
        </h1>

        {{-- Sort Dropdown --}}
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open"
                    class="bg-white border-2 border-border rounded-xl px-5 py-2.5 pr-12 text-left flex items-center gap-2 hover:border-primary transition-colors relative">
                <span>
                    @switch($sort)
                        @case('price_asc') {{ __('shop.sort_price_asc') }} @break
                        @case('price_desc') {{ __('shop.sort_price_desc') }} @break
                        @case('new') {{ __('shop.sort_new') }} @break
                        @default {{ __('shop.sort_popular') }}
                    @endswitch
                </span>
                <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-xs transition-transform"
                   :class="{ 'rotate-180': open }"></i>
            </button>

            <div x-show="open"
                 @click.away="open = false"
                 x-transition
                 class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-border z-10">
                @foreach(['popular' => __('shop.sort_popular'), 'price_asc' => __('shop.sort_price_asc'), 'price_desc' => __('shop.sort_price_desc'), 'new' => __('shop.sort_new')] as $value => $label)
                    <a href="{{ request()->fullUrlWithQuery(['sort' => $value]) }}"
                       class="block w-full px-5 py-3 text-left hover:bg-bg-color transition-colors first:rounded-t-xl last:rounded-b-xl {{ $sort === $value ? 'bg-bg-color' : '' }}">
                        {{ $label }}
                        @if($sort === $value)
                            <i class="fa-solid fa-check text-primary ml-2"></i>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <p class="text-text-muted mb-6">{{ __('shop.delivery_page_desc') }}</p>

    {{-- Products Grid --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-8">
        @forelse($products as $product)
            <x-product-card :product="$product" :inStock="false" />
        @empty
            <div class="col-span-full text-center py-20">
                <i class="fa-solid fa-truck text-6xl text-text-muted mb-4"></i>
                <p class="text-xl text-text-muted">{{ __('shop.delivery_empty') }}</p>
                <a href="{{ route('products.in-car') }}" class="inline-flex items-center gap-2 mt-4 text-green-600 font-semibold hover:underline">
                    <i class="fa-solid fa-car-side"></i> {{ __('shop.browse_in_car') }}
                </a>
            </div>
        @endforelse
    </div>

    @if($products->hasPages())
        <div class="flex justify-center mt-8">
            {{ $products->links() }}
        </div>
    @endif
@endsection
