@extends('layouts.site')

@section('title', $title)

@section('content')
<div class="breadcrumbs">
    <a href="{{ route('home') }}">Главная</a> / <a href="{{ route('category', $categorySlug) }}">{{ $categoryName }}</a> @if($categorySlug) / Все товары @endif
</div>
        
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h1 style="font-size: 36px; font-weight: 800;">{{ $categoryName }}</h1>
    <select class="form-control" style="width: auto; padding: 10px 40px 10px 20px;">
        <option>По популярности</option>
        <option>По цене ↑</option>
        <option>По цене ↓</option>
        <option>Новинки</option>
    </select>
</div>

<div class="product-grid">
    @foreach($products as $product)
        <x-product-card :product="$product" :show-badge="isset($product['inCar']) && $product['inCar']" />
    @endforeach
</div>

<!-- Pagination -->
<div style="display: flex; justify-content: center; gap: 10px; margin: 40px 0 60px;">
    <button class="btn btn-outline" style="min-width: 44px; padding: 0;">1</button>
    <button class="btn btn-primary" style="min-width: 44px; padding: 0;">2</button>
    <button class="btn btn-outline" style="min-width: 44px; padding: 0;">3</button>
    <button class="btn btn-outline" style="min-width: 44px; padding: 0;"><i class="fa-solid fa-chevron-right"></i></button>
</div>
@endsection
