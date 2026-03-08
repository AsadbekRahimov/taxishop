@extends('layouts.site')

@section('title', $title)

@section('content')
<div style="max-width: 900px; padding-top: 30px; padding-bottom: 50px;">
    <h1 style="font-size: 32px; font-weight: 800; margin-bottom: 20px;">Корзина ({{ $cartCount }})</h1>

    @forelse($cartItems as $item)
        <!-- Элемент корзины -->
        <div class="cart-item">
            <img src="{{ $item['product']['image'] }}">
            <div class="cart-item-info">
                <div class="cart-item-title">{{ $item['product']['title'] }}</div>
                <div style="color: var(--text-muted);">{{ $item['product']['price_new'] }} / шт.</div>
            </div>
            <div class="qty-control">
                <button class="qty-btn" onclick="updateQty({{ $item['product']['id'] }}, {{ $item['quantity'] - 1 }})">-</button>
                <input type="number" value="{{ $item['quantity'] }}" class="qty-input" readonly>
                <button class="qty-btn" onclick="updateQty({{ $item['product']['id'] }}, {{ $item['quantity'] + 1 }})">+</button>
            </div>
            <div class="cart-item-total">{{ $item['total'] }}</div>
            <button class="cart-item-remove" onclick="removeFromCart({{ $item['product']['id'] }})"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @empty
        <p style="text-align: center; padding: 50px 0; color: var(--text-muted);">Корзина пуста</p>
    @endforelse

    @if($cartCount > 0)
        <div class="cart-summary-total">Итого: {{ $total }}</div>

        <div style="display: flex; flex-direction: column; gap: 15px;">
            <a href="{{ route('checkout') }}" class="btn btn-primary btn-block" style="padding: 20px; font-size: 24px;">Оформить заказ</a>
            <a href="{{ route('home') }}" class="btn btn-outline btn-block">Продолжить покупки</a>
        </div>
    @endif
</div>

<script>
function updateQty(productId, quantity) {
    if (quantity < 1) return;
    
    fetch('{{ route("cart.update") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function removeFromCart(productId) {
    if (confirm('Удалить товар из корзины?')) {
        fetch('{{ route("cart.remove") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                product_id: productId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}
</script>
@endsection
