@extends('layouts.site')

@section('title', $title)

@section('content')
<div class="thanks-page">
    <div class="thanks-card">
        <div class="check-icon">
            <i class="fa-solid fa-check"></i>
        </div>
        
        <h1 class="thanks-title">Спасибо! Ваш заказ принят</h1>
        <p style="font-size: 18px; color: var(--text-muted);">Водитель уже собирает ваши товары.</p>
        
        <div class="order-number">Заказ: {{ $orderNumber }}</div>
        
        <div style="background: var(--bg-color); padding: 15px; border-radius: var(--radius-md); margin-bottom: 30px;">
            <strong>Способ оплаты:</strong><br>
            💵 Наличные водителю<br>
            <span style="font-size: 20px; font-weight: 800; margin-top: 5px; display: block;">Сумма: {{ Session::get('order_total', '0 сум') }}</span>
        </div>

        <a href="{{ route('home') }}" class="btn btn-primary btn-block">Вернуться в каталог</a>
    </div>
</div>
@endsection
