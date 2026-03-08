@extends('layouts.site')

@section('title', $title)

@section('content')
<div x-data="{
    currentSlide: 0,
    slides: [
        { image: 'https://placehold.co/1200x300/1B5E20/FFFFFF?text=Скидка+20%!', title: 'Скидка 20% на все снеки!', subtitle: 'Только сегодня при покупке в машине' },
        { image: 'https://placehold.co/1200x300/F59E0B/FFFFFF?text=Напитки+2+по+цене+1', title: 'Напитки 2 по цене 1', subtitle: 'При покупке любой еды' },
        { image: 'https://placehold.co/1200x300/0284C7/FFFFFF?text=Доставка+бесплатно', title: 'Доставка бесплатно', subtitle: 'При заказе от 50 000 сум' }
    ],
    autoplayInterval: null,
    
    init() {
        this.startAutoplay();
    },
    
    startAutoplay() {
        this.autoplayInterval = setInterval(() => {
            this.currentSlide = (this.currentSlide + 1) % this.slides.length;
        }, 5000);
    },
    
    stopAutoplay() {
        clearInterval(this.autoplayInterval);
    },
    
    goToSlide(index) {
        this.currentSlide = index;
        this.stopAutoplay();
        this.startAutoplay();
    }
}" @mouseenter="stopAutoplay()" @mouseleave="startAutoplay()">
    
    <!-- Banner Slider -->
    <div class="relative rounded-2xl overflow-hidden mb-8 fade-in-up">
        <div class="relative h-[300px]">
            <template x-for="(slide, index) in slides" :key="index">
                <div class="absolute inset-0 transition-opacity duration-500"
                     :class="{ 'opacity-100': currentSlide === index, 'opacity-0': currentSlide !== index }">
                    <img :src="slide.image" :alt="slide.title" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center text-white text-center p-8">
                        <div>
                            <h2 class="text-4xl font-bold mb-2" x-text="slide.title"></h2>
                            <p class="text-xl" x-text="slide.subtitle"></p>
                        </div>
                    </div>
                </div>
            </template>
        </div>
        
        <!-- Navigation Dots -->
        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex gap-2">
            <template x-for="(slide, index) in slides" :key="index">
                <button @click="goToSlide(index)"
                        class="w-2 h-2 rounded-full transition-all"
                        :class="currentSlide === index ? 'bg-white w-8' : 'bg-white/50'"></button>
            </template>
        </div>
    </div>
    
    <!-- Categories -->
    <section class="mb-8">
        <h2 class="text-2xl font-extrabold mb-5 flex items-center gap-2.5">
            <i class="fa-solid fa-th-large text-primary"></i> Категории
        </h2>
        <div class="grid grid-cols-3 md:grid-cols-4 gap-4">
            <!-- Моковые данные категорий -->
            @foreach([
                ['icon' => 'fa-cookie-bite', 'name' => 'Снеки', 'count' => 24],
                ['icon' => 'fa-bottle-water', 'name' => 'Напитки', 'count' => 18],
                ['icon' => 'fa-ice-cream', 'name' => 'Мороженое', 'count' => 12],
                ['icon' => 'fa-candy-cane', 'name' => 'Сладости', 'count' => 31],
                ['icon' => 'fa-mug-hot', 'name' => 'Горячее', 'count' => 8],
                ['icon' => 'fa-pizza-slice', 'name' => 'Фастфуд', 'count' => 15],
                ['icon' => 'fa-apple-whole', 'name' => 'Фрукты', 'count' => 20],
                ['icon' => 'fa-battery-full', 'name' => 'Аккумуляторы', 'count' => 5]
            ] as $category)
            <a href="{{ route('category', ['slug' => Str::slug($category['name'])]) }}" 
               class="bg-white rounded-2xl p-5 text-center hover:shadow-lg transition-all hover:scale-105 active:scale-95">
                <div class="text-3xl mb-2 text-primary">
                    <i class="fa-solid {{ $category['icon'] }}"></i>
                </div>
                <div class="font-semibold text-sm">{{ $category['name'] }}</div>
                <div class="text-xs text-text-muted mt-1">{{ $category['count'] }} товаров</div>
            </a>
            @endforeach
        </div>
    </section>
    
    <!-- In Car Products -->
    <section class="mb-8">
        <h2 class="text-2xl font-extrabold mb-5 flex items-center gap-2.5">
            <i class="fa-solid fa-bolt text-accent"></i> Есть в машине
        </h2>
        <div class="relative">
            <!-- Scroll Container -->
            <div class="flex gap-4 overflow-x-auto scroll-smooth scrollbar-hide pb-4" 
                 style="scroll-snap-type: x mandatory;">
                <!-- Моковые товары "в машине" -->
                @foreach([
                    ['name' => 'Lays Классические', 'price' => 12000, 'oldPrice' => 15000, 'image' => 'lays'],
                    ['name' => 'Coca-Cola 0.5л', 'price' => 8000, 'oldPrice' => null, 'image' => 'cola'],
                    ['name' => 'Snickers', 'price' => 6000, 'oldPrice' => 7000, 'image' => 'snickers'],
                    ['name' => 'KitKat', 'price' => 6500, 'oldPrice' => null, 'image' => 'kitkat'],
                    ['name' => 'Red Bull', 'price' => 18000, 'oldPrice' => 20000, 'image' => 'redbull'],
                    ['name' => 'Pringles', 'price' => 22000, 'oldPrice' => 25000, 'image' => 'pringles']
                ] as $product)
                <div class="flex-none w-64 bg-white rounded-2xl p-4 shadow-sm hover:shadow-lg transition-all"
                     style="scroll-snap-align: start;">
                    <!-- Badge -->
                    <span class="absolute -mt-2 ml-2 bg-primary text-white text-xs font-bold px-3 py-1 rounded-full">
                        В машине
                    </span>
                    
                    <!-- Product Image -->
                    <img src="https://placehold.co/200x180/F3F4F6/1B5E20?text={{ $product['image'] }}" 
                         :alt="'{{ $product['name'] }}'"
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
            
            <!-- Scroll Arrows -->
            <button class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-3 bg-white rounded-full w-10 h-10 shadow-lg flex items-center justify-center hover:scale-110 transition-transform">
                <i class="fa-solid fa-chevron-left text-primary"></i>
            </button>
            <button class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-3 bg-white rounded-full w-10 h-10 shadow-lg flex items-center justify-center hover:scale-110 transition-transform">
                <i class="fa-solid fa-chevron-right text-primary"></i>
            </button>
        </div>
    </section>
    
    <!-- Bestsellers -->
    <section class="mb-8">
        <h2 class="text-2xl font-extrabold mb-5 flex items-center gap-2.5">
            <i class="fa-solid fa-fire text-accent"></i> Хиты продаж
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <!-- Моковые товары хитов продаж -->
            @foreach([
                ['name' => 'Lays Со сметаной и луком', 'price' => 12000, 'oldPrice' => 15000, 'inCar' => true, 'image' => 'lays2'],
                ['name' => 'Fanta 0.5л', 'price' => 8000, 'oldPrice' => null, 'inCar' => false, 'image' => 'fanta'],
                ['name' => 'Mars', 'price' => 6000, 'oldPrice' => 7000, 'inCar' => true, 'image' => 'mars'],
                ['name' => 'Twix', 'price' => 6500, 'oldPrice' => null, 'inCar' => true, 'image' => 'twix'],
                ['name' => 'Sprite 0.5л', 'price' => 8000, 'oldPrice' => 9000, 'inCar' => false, 'image' => 'sprite'],
                ['name' => 'Bounty', 'price' => 6500, 'oldPrice' => null, 'inCar' => true, 'image' => 'bounty'],
                ['name' => 'Doritos', 'price' => 14000, 'oldPrice' => 16000, 'inCar' => false, 'image' => 'doritos'],
                ['name' => 'Mirinda 0.5л', 'price' => 8000, 'oldPrice' => null, 'inCar' => false, 'image' => 'mirinda']
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
    </section>
</div>

<!-- Alpine Store для корзины -->
<script>
document.addEventListener('alpine:init', () => {
    Alpine.store('cart', {
        items: [],
        
        add(product) {
            this.items.push({...product, quantity: 1});
        }
    });
});
</script>

<style>
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection
