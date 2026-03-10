@props(['product', 'inStock' => false])

<div class="bg-white rounded-2xl p-4 shadow-sm hover:shadow-lg transition-all hover:scale-105 active:scale-95 relative">
    @if($inStock)
        <span class="absolute top-2 left-2 bg-primary text-white text-xs font-bold px-3 py-1 rounded-full z-10">
            {{ __('shop.in_car_badge') }}
        </span>
    @endif

    @if($product->old_price)
        <span class="absolute top-2 right-2 bg-accent text-white text-xs font-bold px-2 py-1 rounded-lg z-10">
            -{{ round(($product->old_price - $product->price) / $product->old_price * 100) }}%
        </span>
    @endif

    <a href="{{ route('product.show', $product->slug) }}">
        <img src="{{ $product->main_image ? asset('storage/' . $product->main_image) : 'https://placehold.co/200x180/F3F4F6/1B5E20?text=' . urlencode($product->name) }}"
             alt="{{ $product->name }}"
             class="w-full h-44 object-cover rounded-xl mb-3"
             loading="lazy">
    </a>

    <a href="{{ route('product.show', $product->slug) }}">
        <h3 class="font-bold text-sm mb-2 line-clamp-2">{{ $product->name }}</h3>
    </a>

    <div class="mb-3">
        <span class="text-xl font-extrabold text-primary">{{ number_format((float) $product->price, 0, ',', ' ') }} {{ __('shop.currency') }}</span>
        @if($product->old_price)
            <span class="text-sm text-text-muted line-through ml-2">{{ number_format((float) $product->old_price, 0, ',', ' ') }} {{ __('shop.currency') }}</span>
        @endif
    </div>

    <form action="{{ route('cart.add') }}" method="POST">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">
        <input type="hidden" name="qty" value="1">
        <button type="submit"
                class="w-full bg-primary text-white font-bold py-2.5 rounded-xl hover:bg-primary-light transition-colors active:scale-95"
                @click="cartCount++">
            <i class="fa-solid fa-cart-plus mr-2"></i> {{ __('shop.add_to_cart') }}
        </button>
    </form>
</div>
