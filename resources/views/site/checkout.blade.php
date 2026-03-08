@extends('layouts.site')

@section('title', $title)

@section('content')
<div style="padding-top: 30px; padding-bottom: 50px;">
    <h1 style="font-size: 32px; font-weight: 800; margin-bottom: 20px;">Оформление заказа</h1>

    <form action="{{ route('order.place') }}" method="POST">
        @csrf
        <div class="checkout-layout">
            <!-- Левая часть 60% (Форма) -->
            <div class="checkout-form">
                <div class="form-group">
                    <label>Ваше имя</label>
                    <input type="text" name="name" class="form-control" placeholder="Например: Алишер" value="{{ old('name') }}" required>
                </div>
                <div class="form-group">
                    <label>Номер телефона</label>
                    <input type="tel" name="phone" class="form-control" placeholder="+998 __ ___ __ __" value="{{ old('phone') }}" required>
                </div>

                <div class="form-group" style="margin-top: 30px;">
                    <label style="font-size: 20px; color: var(--text-main);">Способ оплаты</label>
                    <div class="payment-methods">
                        <label class="payment-method active">
                            <input type="radio" name="payment" value="cash" checked>
                            <i class="fa-solid fa-money-bill-wave"></i> 💵 Наличные водителю
                        </label>
                        <label class="payment-method">
                            <input type="radio" name="payment" value="qr">
                            <i class="fa-solid fa-qrcode"></i> 📱 QR-код (Paynet / Xolis)
                        </label>
                        <label class="payment-method">
                            <input type="radio" name="payment" value="delivery">
                            <i class="fa-solid fa-truck"></i> 🚚 Оплата при доставке
                        </label>
                    </div>
                </div>

                <!-- Блок адреса (Визуально показываем) -->
                <div class="form-group" style="margin-top: 20px;">
                    <label>Адрес доставки (только для заказа на дом)</label>
                    <textarea name="address" class="form-control" rows="3" placeholder="Укажите район, улицу, дом, квартиру...">{{ old('address') }}</textarea>
                </div>
            </div>

            <!-- Правая часть 40% (Сводка) -->
            <div class="checkout-summary">
                <h3 style="font-size: 24px; margin-bottom: 20px;">Ваш заказ</h3>
                
                @foreach($cartItems as $item)
                    <div style="display: flex; justify-content: space-between; margin-bottom: 15px; border-bottom: 1px solid var(--border); padding-bottom: 10px;">
                        <div>{{ $item['product']['title'] }} <span style="color:var(--text-muted)">x{{ $item['quantity'] }}</span></div>
                        <div style="font-weight: 700;">{{ $item['total'] }}</div>
                    </div>
                @endforeach

                <input type="hidden" name="total" value="{{ $total }}">

                <div style="display: flex; justify-content: space-between; align-items: center; margin: 30px 0;">
                    <div style="font-size: 20px; color: var(--text-muted);">К оплате:</div>
                    <div style="font-size: 32px; font-weight: 800; color: var(--primary);">{{ $total }}</div>
                </div>

                <button type="submit" class="btn btn-primary btn-block" style="font-size: 20px;">Подтвердить заказ</button>
            </div>
        </div>
    </form>
</div>

<!-- Простой скрипт для смены активного класса у радио-кнопок -->
<script>
    document.querySelectorAll('.payment-method input').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.payment-method').forEach(el => el.classList.remove('active'));
            if(this.checked) this.closest('.payment-method').classList.add('active');
        });
    });
</script>
@endsection
