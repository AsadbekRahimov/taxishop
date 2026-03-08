@extends('layouts.site')

@section('title', $title)

@section('content')
<!-- Banner -->
<div class="hero-banner">
    <img src="https://placehold.co/1200x300/1B5E20/FFFFFF?text=+" alt="Banner">
    <div class="hero-text">
        <h2>Скидка 20% на все снеки!</h2>
        <p style="font-size: 20px;">Только сегодня при покупке в машине.</p>
    </div>
</div>

<!-- Categories -->
<h2 class="section-title">Категории</h2>
<div class="categories-grid">
    @foreach($categories as $category)
        <x-category-item :category="$category" :icon="$category['icon']" />
    @endforeach
</div>

<!-- In Car Scroll -->
<h2 class="section-title"><i class="fa-solid fa-bolt" style="color: var(--accent)"></i> Есть в машине</h2>
<div class="horizontal-scroll">
    @foreach($inCarProducts as $product)
        <x-product-card :product="$product" :show-badge="true" />
    @endforeach
</div>

<!-- Bestsellers -->
<h2 class="section-title">Хиты продаж</h2>
<div class="product-grid" style="margin-bottom: 50px;">
    @foreach($bestsellerProducts as $product)
        <x-product-card :product="$product" :show-badge="isset($product['inCar']) && $product['inCar']" />
    @endforeach
</div>
@endsection
