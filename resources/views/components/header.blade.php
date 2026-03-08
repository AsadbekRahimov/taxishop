@props(['cartCount' => 0, 'driverName' => 'Иван Петров'])

<header class="header">
    <div class="container">
        <a href="{{ route('home') }}" class="logo">TaxiShop <i class="fa-solid fa-taxi"></i></a>
        <div class="driver-info"><i class="fa-solid fa-id-badge"></i> Водитель: {{ $driverName }}</div>
        <div class="header-actions">
            <a href="{{ route('cart') }}" class="cart-btn">
                <i class="fa-solid fa-shopping-cart"></i>
                @if($cartCount > 0)
                    <span class="cart-badge">{{ $cartCount }}</span>
                @endif
            </a>
            <a href="{{ route('login') }}" class="logout-btn"><i class="fa-solid fa-arrow-right-from-bracket"></i></a>
        </div>
    </div>
</header>
