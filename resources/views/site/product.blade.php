@extends('layouts.site')

@section('title', $title)

@section('content')
<!-- Верхний блок (Сетка 60/40) -->
<div class="pdp-layout" style="margin-top: 20px;">
    
    <!-- Левая колонка 60% -->
    <div class="pdp-left">
        <img src="{{ $product['image'] }}" class="pdp-main-img" alt="{{ $product['title'] }}">
        
        <!-- Нижний блок: 4 миниатюры -->
        <div class="pdp-thumbnails">
            <img src="https://placehold.co/200x200/E5E7EB/A3A8B8?text=1" class="active" alt="thumb">
            <img src="https://placehold.co/200x200/E5E7EB/A3A8B8?text=2" alt="thumb">
            <img src="https://placehold.co/200x200/E5E7EB/A3A8B8?text=3" alt="thumb">
            <img src="https://placehold.co/200x200/E5E7EB/A3A8B8?text=4" alt="thumb">
        </div>
    </div>

    <!-- Правая колонка 40% -->
    <div class="pdp-right">
        <div class="breadcrumbs" style="padding-top: 0;">
            <a href="{{ route('home') }}">Каталог</a> / <a href="{{ route('category') }}">Электроника</a> / Кабели
        </div>
        
        <h1 class="pdp-title">{{ $product['title'] }}</h1>
        
        <div class="pdp-price-wrap">
            <span class="pdp-price-new">{{ $product['price_new'] }}</span>
            @if($product['price_old'])
                <span class="pdp-price-old">{{ $product['price_old'] }}</span>
                <?php 
                $priceNew = (int) str_replace([' ', 'сум'], '', $product['price_new']);
                $priceOld = (int) str_replace([' ', 'сум'], '', $product['price_old']);
                $discount = round((($priceOld - $priceNew) / $priceOld) * 100);
                ?>
                <span class="pdp-discount">-{{ $discount }}%</span>
            @endif
        </div>

        <div class="pdp-desc">
            <strong>О товаре:</strong><br><br>
            Высококачественный кабель для быстрой зарядки устройств с разъемом Type-C. Поддерживает передачу данных. 
            <br><br>
            • Длина: 1 метр<br>
            • Цвет: Белый<br>
            • Сила тока: 3A (Fast Charge)
        </div>

        <div class="pdp-actions">
            <button class="btn btn-primary btn-block"><i class="fa-solid fa-hand-holding-dollar"></i> Купить у водителя</button>
            <button class="btn btn-blue btn-block"><i class="fa-solid fa-qrcode"></i> Оплатить через QR-код (Paynet)</button>
            <button class="btn btn-accent btn-block"><i class="fa-solid fa-truck"></i> Заказать на дом</button>
        </div>
    </div>
</div>
@endsection
