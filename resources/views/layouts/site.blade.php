<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'TaxiShop' }} - TaxiShop</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 384 512%22><path fill=%22%231B5E20%22 d=%22M192 0C85.97 0 0 85.97 0 192c0 77.41 26.97 99.03 172.3 309.7C177.8 511.5 185.1 512 192 512s14.19-.5234 19.69-2.305C357 299.1 384 277.4 384 192C384 85.97 298 0 192 0zM192 463.6C55.83 277.5 32 255.9 32 192c0-88.22 71.78-160 160-160s160 71.78 160 160C352 256.8 326.5 279.1 192 463.6zM192 111.1c-44.18 0-80 35.82-80 80s35.82 80 80 80s80-35.82 80-80S236.2 111.1 192 111.1z%22/></svg>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="{{ $bodyClass ?? '' }}">
    @if(!isset($hideHeader) || !$hideHeader)
        <x-header :cart-count="$cartCount ?? 0" :driver-name="$driverName ?? 'Иван Петров'" />
    @endif

    <div class="container">
        @yield('content')
    </div>
</body>
</html>
