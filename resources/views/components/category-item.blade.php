@props(['category', 'icon' => null])

<a href="{{ route('category', ['slug' => $category['slug'] ?? '#']) }}" class="category-item">
    <i class="{{ $icon ?? $category['icon'] ?? 'fa-solid fa-box' }}"></i>
    <span>{{ $category['name'] ?? 'Category' }}</span>
</a>
