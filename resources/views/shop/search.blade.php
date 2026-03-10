@extends('layouts.shop')

@section('title', $q ? __('shop.search_results', ['query' => $q]) : __('shop.search'))

@section('content')
    <h1 class="text-3xl font-extrabold mb-6">
        @if($q)
            {{ __('shop.search_results', ['query' => $q]) }}
        @else
            {{ __('shop.search_products') }}
        @endif
    </h1>

    {{-- Search Form --}}
    <form action="{{ route('search') }}" method="GET" class="mb-8">
        <div class="flex gap-3">
            <input type="text" name="q" value="{{ $q }}"
                   class="flex-1 px-5 py-3 border-2 border-border rounded-xl text-lg outline-none transition-colors focus:border-primary bg-white"
                   placeholder="{{ __('shop.search_input_placeholder') }}">
            <button type="submit"
                    class="bg-primary text-white font-bold px-8 py-3 rounded-xl hover:bg-primary-light transition-colors">
                <i class="fa-solid fa-search mr-2"></i> {{ __('shop.find') }}
            </button>
        </div>
    </form>

    {{-- Results --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-8">
        @forelse($products as $product)
            <x-product-card :product="$product" :inStock="in_array($product->id, $inStockProductIds)" />
        @empty
            <div class="col-span-full text-center py-20">
                <i class="fa-solid fa-search text-6xl text-text-muted mb-4"></i>
                <p class="text-xl text-text-muted mb-2">
                    @if($q)
                        {{ __('shop.search_not_found', ['query' => $q]) }}
                    @else
                        {{ __('shop.enter_search_query') }}
                    @endif
                </p>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 mt-4 text-primary font-semibold hover:underline">
                    <i class="fa-solid fa-arrow-left"></i> {{ __('shop.back_to_home') }}
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
