<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'TaxiShop' }} - TaxiShop</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 384 512%22><path fill=%22%231B5E20%22 d=%22M192 0C85.97 0 0 85.97 0 192c0 77.41 26.97 99.03 172.3 309.7C177.8 511.5 185.1 512 192 512s14.19-.5234 19.69-2.305C357 299.1 384 277.4 384 192C384 85.97 298 0 192 0zM192 463.6C55.83 277.5 32 255.9 32 192c0-88.22 71.78-160 160-160s160 71.78 160 160C352 256.8 326.5 279.1 192 463.6zM192 111.1c-44.18 0-80 35.82-80 80s35.82 80 80 80s80-35.82 80-80S236.2 111.1 192 111.1z%22/></svg>">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Конфигурация Tailwind с цветами TaxiShop -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1B5E20',
                        'primary-light': '#2E7D32',
                        accent: '#F59E0B',
                        blue: '#0284C7',
                        'bg-color': '#F3F4F6',
                        'text-main': '#1F2937',
                        'text-muted': '#6B7280',
                        border: '#E5E7EB'
                    },
                    fontFamily: {
                        'nunito': ['Nunito', 'sans-serif']
                    }
                }
            }
        }
    </script>
    
    <style>
        body { font-family: 'Nunito', sans-serif; }
        
        /* Анимация для галочки на странице спасибо */
        @keyframes checkmark {
            0% { stroke-dashoffset: 100; }
            100% { stroke-dashoffset: 0; }
        }
        .checkmark-animate {
            stroke-dasharray: 100;
            animation: checkmark 0.5s ease-in-out forwards;
        }
        
        /* Анимация появления */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .fade-in-up {
            animation: fadeInUp 0.5s ease-out;
        }
    </style>
</head>
<body class="bg-bg-color text-text-main" x-data="{ cartCount: {{ $cartCount ?? 0 }} }">
    @if(!isset($hideHeader) || !$hideHeader)
        <!-- Header -->
        <header class="bg-white shadow-sm sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-5">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo -->
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5 text-2xl font-extrabold text-primary">
                        TaxiShop <i class="fa-solid fa-taxi text-accent"></i>
                    </a>
                    
                    <!-- Driver Info -->
                    <div class="bg-bg-color px-4 py-2 rounded-full text-sm font-semibold text-text-muted">
                        <i class="fa-solid fa-id-badge mr-2"></i> Водитель: {{ $driverName ?? 'Иван Петров' }}
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex items-center gap-5">
                        <!-- Cart -->
                        <a href="{{ route('cart') }}" class="relative text-primary bg-bg-color w-12 h-12 flex items-center justify-center rounded-full">
                            <i class="fa-solid fa-shopping-cart text-xl"></i>
                            <template x-if="cartCount > 0">
                                <span class="absolute -top-1.5 -right-1.5 bg-accent text-white text-xs font-bold w-5.5 h-5.5 flex items-center justify-center rounded-full" x-text="cartCount"></span>
                            </template>
                        </a>
                        
                        <!-- Logout -->
                        <a href="{{ route('login') }}" class="text-text-muted text-xl p-2.5">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        </a>
                    </div>
                </div>
            </div>
        </header>
    @endif

    <main class="max-w-7xl mx-auto px-5 py-6">
        @yield('content')
    </main>
    
    <!-- Footer -->
    @if(!isset($hideFooter) || !$hideFooter)
        <footer class="bg-white border-t border-border mt-12">
            <div class="max-w-7xl mx-auto px-5 py-6 text-center text-text-muted">
                © TaxiShop 2026
            </div>
        </footer>
    @endif
</body>
</html>
