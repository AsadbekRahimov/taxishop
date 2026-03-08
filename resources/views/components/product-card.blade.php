@props(['product', 'showBadge' => false])

<a href="{{ route('product', ['id' => $product['id'] ?? '#']) }}" class="product-card">
    @if($showBadge && isset($product['inCar']) && $product['inCar'])
        <span class="product-badge">В машине</span>
    @endif
    <img src="{{ $product['image'] ?? 'https://placehold.co/400x400/E5E7EB/A3A8B8?text=Product' }}" class="product-img" alt="{{ $product['title'] ?? 'Product' }}">
    <div class="product-title">{{ $product['title'] ?? 'Product Title' }}</div>
    <div class="product-price-block">
        <span class="price-new">{{ $product['price_new'] ?? '0 сум' }}</span>
        @if(isset($product['price_old']))
            <span class="price-old">{{ $product['price_old'] }}</span>
        @endif
    </div>
</a>
