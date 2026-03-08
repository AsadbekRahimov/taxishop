@props(['showActions' => true])

@if($showActions)
    <div class="container" style="padding-top: 30px; padding-bottom: 50px;">
        <div style="display: flex; flex-direction: column; gap: 15px;">
            <a href="{{ route('checkout') }}" class="btn btn-primary btn-block" style="padding: 20px; font-size: 24px;">Оформить заказ</a>
            <a href="{{ route('home') }}" class="btn btn-outline btn-block">Продолжить покупки</a>
        </div>
    </div>
@endif
