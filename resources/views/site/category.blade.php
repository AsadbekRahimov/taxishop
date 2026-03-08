@extends('layouts.site')

@section('title', $title)

@section('content')
<div x-data="{
    sortBy: 'popular',
    currentPage: 2,
    totalPages: 5,
    
    sortOptions: [
        { value: 'popular', label: 'По популярности' },
        { value: 'price_asc', label: 'По цене ↑' },
        { value: 'price_desc', label: 'По цене ↓' },
        { value: 'newest', label: 'Новинки' }
    ],
    
    changeSort(value) {
        this.sortBy = value;
        // Здесь будет логика сортировки
        console.log('Sort by:', value);
    },
    
    goToPage(page) {
        this.currentPage = page;
        // Здесь будет логика пагинации
        console.log('Go to page:', page);
    }
}">
    
    <!-- Breadcrumbs -->
    <nav class="flex items-center gap-2 text-sm text-text-muted mb-6">
        <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Главная</a>
        <i class="fa-solid fa-chevron-right text-xs"></i>
        <a href="{{ route('category', ['slug' => 'snacks']) }}" class="hover:text-primary transition-colors">Снеки</a>
        <i class="fa-solid fa-chevron-right text-xs"></i>
        <span class="text-text-main">Все товары</span>
    </nav>
    
    <!-- Header with Sort -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl md:text-4xl font-extrabold">Снеки</h1>
        
        <!-- Sort Dropdown -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" 
                    class="bg-white border-2 border-border rounded-xl px-5 py-2.5 pr-12 text-left flex items-center gap-2 hover:border-primary transition-colors">
                <span x-text="sortOptions.find(o => o.value === sortBy).label"></span>
                <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-xs transition-transform"
                   :class="{ 'rotate-180': open }"></i>
            </button>
            
            <div x-show="open" 
                 @click.away="open = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-border z-10">
                <template x-for="option in sortOptions" :key="option.value">
                    <button @click="changeSort(option.value); open = false"
                            class="w-full px-5 py-3 text-left hover:bg-bg-color transition-colors first:rounded-t-xl last:rounded-b-xl"
                            :class="{ 'bg-bg-color': sortBy === option.value }">
                        <span x-text="option.label"></span>
                        <i class="fa-solid fa-check text-primary ml-auto"
                           x-show="sortBy === option.value"></i>
                    </button>
                </template>
            </div>
        </div>
    </div>
    
    <!-- Products Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-8">
        <!-- Моковые товары категории -->
        @foreach([
            ['name' => 'Lays Классические', 'price' => 12000, 'oldPrice' => 15000, 'inCar' => true, 'image' => 'lays'],
            ['name' => 'Pringles Original', 'price' => 22000, 'oldPrice' => 25000, 'inCar' => false, 'image' => 'pringles'],
            ['name' => 'Doritos Nacho Cheese', 'price' => 14000, 'oldPrice' => null, 'inCar' => true, 'image' => 'doritos'],
            ['name' => 'Cheetos', 'price' => 10000, 'oldPrice' => 12000, 'inCar' => false, 'image' => 'cheetos'],
            ['name' => 'Lays Со сметаной и луком', 'price' => 12000, 'oldPrice' => null, 'inCar' => true, 'image' => 'lays2'],
            ['name' => 'Popcorn карамель', 'price' => 8000, 'oldPrice' => 10000, 'inCar' => false, 'image' => 'popcorn'],
            ['name' => 'Сухарики ржаные', 'price' => 6000, 'oldPrice' => null, 'inCar' => true, 'image' => 'suhariki'],
            ['name' => 'Орешки соленые', 'price' => 15000, 'oldPrice' => 18000, 'inCar' => false, 'image' => 'oreshek']
        ] as $product)
            <div class="bg-white rounded-2xl p-4 shadow-sm hover:shadow-lg transition-all hover:scale-105 active:scale-95 relative">
                <!-- Badge if in car -->
                @if($product['inCar'])
                    <span class="absolute -mt-2 ml-2 bg-primary text-white text-xs font-bold px-3 py-1 rounded-full">
                        В машине
                    </span>
                @endif
                
                <!-- Product Image -->
                <img src="https://placehold.co/200x180/F3F4F6/1B5E20?text={{ $product['image'] }}" 
                     alt="{{ $product['name'] }}"
                     class="w-full h-44 object-cover rounded-xl mb-3"
                     loading="lazy">
                
                <!-- Product Info -->
                <h3 class="font-bold text-sm mb-2 line-clamp-2">{{ $product['name'] }}</h3>
                
                <!-- Price -->
                <div class="mb-3">
                    <span class="text-xl font-extrabold text-primary">{{ number_format($product['price'], 0, ',', ' ') }} сум</span>
                    @if($product['oldPrice'])
                        <span class="text-sm text-text-muted line-through ml-2">{{ number_format($product['oldPrice'], 0, ',', ' ') }} сум</span>
                    @endif
                </div>
                
                <!-- Add to Cart Button -->
                <button class="w-full bg-primary text-white font-bold py-2.5 rounded-xl hover:bg-primary-light transition-colors active:scale-95"
                        @click="$store.cart.add({{ json_encode($product) }}); cartCount++">
                    <i class="fa-solid fa-cart-plus mr-2"></i> В корзину
                </button>
            </div>
        @endforeach
    </div>
    
    <!-- Pagination -->
    <div class="flex justify-center items-center gap-2 mt-12">
        <!-- Previous -->
        <button @click="goToPage(currentPage - 1)"
                :disabled="currentPage === 1"
                class="w-11 h-11 rounded-xl border-2 border-border bg-white flex items-center justify-center hover:border-primary transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
            <i class="fa-solid fa-chevron-left"></i>
        </button>
        
        <!-- Page Numbers -->
        <template x-for="page in totalPages" :key="page">
            <button @click="goToPage(page)"
                    class="w-11 h-11 rounded-xl font-bold transition-all"
                    :class="currentPage === page 
                        ? 'bg-primary text-white' 
                        : 'border-2 border-border bg-white hover:border-primary'">
                <span x-text="page"></span>
            </button>
        </template>
        
        <!-- Next -->
        <button @click="goToPage(currentPage + 1)"
                :disabled="currentPage === totalPages"
                class="w-11 h-11 rounded-xl border-2 border-border bg-white flex items-center justify-center hover:border-primary transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
            <i class="fa-solid fa-chevron-right"></i>
        </button>
    </div>
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection
