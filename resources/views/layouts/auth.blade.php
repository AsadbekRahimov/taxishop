<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('shop.login_title') }} - TaxiShop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1B5E20',
                        'primary-light': '#2E7D32',
                        accent: '#F59E0B',
                        'bg-color': '#F3F4F6',
                        'text-main': '#1F2937',
                        'text-muted': '#6B7280',
                        border: '#E5E7EB'
                    }
                }
            }
        }
    </script>

    <style>body { font-family: 'Nunito', sans-serif; }</style>
</head>
<body class="bg-bg-color min-h-screen flex items-center justify-center">
    {{-- Language Switcher --}}
    <div class="fixed top-4 right-4" x-data="{ open: false }">
        <button @click="open = !open"
                class="bg-white px-3 py-2 rounded-full text-sm font-semibold text-text-muted hover:bg-gray-100 transition-colors flex items-center gap-1.5 shadow-sm">
            <i class="fa-solid fa-globe"></i>
            <span>{{ strtoupper(app()->getLocale()) }}</span>
            <i class="fa-solid fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': open }"></i>
        </button>
        <div x-show="open" @click.away="open = false" x-transition
             class="absolute right-0 mt-2 w-36 bg-white rounded-xl shadow-lg border border-border z-50">
            @foreach(['ru' => 'Русский', 'en' => 'English', 'uz' => 'O\'zbek'] as $code => $label)
                <a href="{{ route('locale.switch', $code) }}"
                   class="block px-4 py-2.5 text-sm hover:bg-bg-color transition-colors first:rounded-t-xl last:rounded-b-xl {{ app()->getLocale() === $code ? 'bg-bg-color font-bold text-primary' : '' }}">
                    {{ $label }}
                    @if(app()->getLocale() === $code)
                        <i class="fa-solid fa-check text-primary ml-1"></i>
                    @endif
                </a>
            @endforeach
        </div>
    </div>

    @yield('content')

    <script src="https://unpkg.com/alpinejs" defer></script>
</body>
</html>
