@props(['breadcrumbs' => [], 'current' => ''])

<nav class="flex items-center gap-2 text-sm text-text-muted mb-6">
    <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Главная</a>

    @foreach($breadcrumbs as $crumb)
        <i class="fa-solid fa-chevron-right text-xs"></i>
        @if(!$loop->last || $current)
            <a href="{{ route('category.show', $crumb->slug) }}" class="hover:text-primary transition-colors">{{ $crumb->name }}</a>
        @else
            <span class="text-text-main">{{ $crumb->name }}</span>
        @endif
    @endforeach

    @if($current)
        <i class="fa-solid fa-chevron-right text-xs"></i>
        <span class="text-text-main">{{ $current }}</span>
    @endif
</nav>
