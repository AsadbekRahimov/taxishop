<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TaxiShop') - {{ __('shop.driver_panel') }}</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 384 512%22><path fill=%22%231B5E20%22 d=%22M192 0C85.97 0 0 85.97 0 192c0 77.41 26.97 99.03 172.3 309.7C177.8 511.5 185.1 512 192 512s14.19-.5234 19.69-2.305C357 299.1 384 277.4 384 192C384 85.97 298 0 192 0zM192 463.6C55.83 277.5 32 255.9 32 192c0-88.22 71.78-160 160-160s160 71.78 160 160C352 256.8 326.5 279.1 192 463.6zM192 111.1c-44.18 0-80 35.82-80 80s35.82 80 80 80s80-35.82 80-80S236.2 111.1 192 111.1z%22/></svg>">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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
        .fade-in-up { animation: fadeInUp 0.3s ease-out; }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-bg-color text-text-main">
    <header class="bg-primary shadow-sm sticky top-0 z-50">
        <div class="max-w-lg mx-auto px-4">
            <div class="flex justify-between items-center h-14">
                <div class="flex items-center gap-2 text-white">
                    <i class="fa-solid fa-taxi text-accent"></i>
                    <span class="font-bold text-lg">{{ __('shop.driver_panel') }}</span>
                </div>

                <div class="flex items-center gap-3">
                    {{-- Language Switcher --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open"
                                class="bg-white/20 px-2.5 py-1.5 rounded-full text-xs font-semibold text-white hover:bg-white/30 transition-colors flex items-center gap-1">
                            <i class="fa-solid fa-globe"></i>
                            <span>{{ strtoupper(app()->getLocale()) }}</span>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition
                             class="absolute right-0 mt-2 w-32 bg-white rounded-xl shadow-lg border border-border z-50">
                            @foreach(['ru' => 'Русский', 'en' => 'English', 'uz' => 'O\'zbek'] as $code => $label)
                                <a href="{{ route('locale.switch', $code) }}"
                                   class="block px-3 py-2 text-sm hover:bg-bg-color transition-colors first:rounded-t-xl last:rounded-b-xl {{ app()->getLocale() === $code ? 'bg-bg-color font-bold text-primary' : '' }}">
                                    {{ $label }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="text-white/80 text-sm font-semibold hidden sm:block">
                        <i class="fa-solid fa-user mr-1"></i>
                        {{ auth()->user()?->name }}
                    </div>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-white/70 hover:text-white transition-colors">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-lg mx-auto px-4 py-4">
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm fade-in-up">
                <i class="fa-solid fa-check-circle mr-1"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm fade-in-up">
                <i class="fa-solid fa-exclamation-circle mr-1"></i> {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
