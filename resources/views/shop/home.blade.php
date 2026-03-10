@extends('layouts.shop')

@section('title', __('shop.home'))

@section('content')
<div x-data="{
    currentSlide: 0,
    slides: [
        { image: 'https://placehold.co/1200x300/1B5E20/FFFFFF?text=Скидка+20%!', title: 'Скидка 20% на все снеки!', subtitle: 'Только сегодня при покупке в машине' },
        { image: 'https://placehold.co/1200x300/F59E0B/FFFFFF?text=Напитки+2+по+цене+1', title: 'Напитки 2 по цене 1', subtitle: 'При покупке любой еды' },
        { image: 'https://placehold.co/1200x300/0284C7/FFFFFF?text=Доставка+бесплатно', title: 'Доставка бесплатно', subtitle: 'При заказе от 50 000 сум' }
    ],
    autoplayInterval: null,
    init() { this.startAutoplay(); },
    startAutoplay() { this.autoplayInterval = setInterval(() => { this.currentSlide = (this.currentSlide + 1) % this.slides.length; }, 5000); },
    stopAutoplay() { clearInterval(this.autoplayInterval); },
    goToSlide(index) { this.currentSlide = index; this.stopAutoplay(); this.startAutoplay(); }
}" @mouseenter="stopAutoplay()" @mouseleave="startAutoplay()">

    {{-- Banner Slider --}}
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
        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex gap-2">
            <template x-for="(slide, index) in slides" :key="index">
                <button @click="goToSlide(index)"
                        class="w-2 h-2 rounded-full transition-all"
                        :class="currentSlide === index ? 'bg-white w-8' : 'bg-white/50'"></button>
            </template>
        </div>
    </div>

    {{-- Categories --}}
    @if($categories->isNotEmpty())
    <section class="mb-8">
        <h2 class="text-2xl font-extrabold mb-5 flex items-center gap-2.5">
            <i class="fa-solid fa-th-large text-primary"></i> {{ __('shop.categories') }}
        </h2>
        <div class="grid grid-cols-3 md:grid-cols-4 gap-4">
            @foreach($categories as $category)
                <a href="{{ route('category.show', $category->slug) }}"
                   class="bg-white rounded-2xl p-5 text-center hover:shadow-lg transition-all hover:scale-105 active:scale-95">
                    <div class="text-3xl mb-2 text-primary">
                        @if($category->icon)
                            <img src="{{ asset('storage/' . $category->icon) }}" alt="{{ $category->name }}" class="w-10 h-10 mx-auto">
                        @else
                            <i class="fa-solid fa-box"></i>
                        @endif
                    </div>
                    <div class="font-semibold text-sm">{{ $category->name }}</div>
                    <div class="text-xs text-text-muted mt-1">{{ __('shop.products_count', ['count' => $category->products_count ?? $category->products()->count()]) }}</div>
                </a>

                @foreach($category->children as $child)
                    <a href="{{ route('category.show', $child->slug) }}"
                       class="bg-white rounded-2xl p-5 text-center hover:shadow-lg transition-all hover:scale-105 active:scale-95">
                        <div class="text-3xl mb-2 text-primary">
                            @if($child->icon)
                                <img src="{{ asset('storage/' . $child->icon) }}" alt="{{ $child->name }}" class="w-10 h-10 mx-auto">
                            @else
                                <i class="fa-solid fa-box-open"></i>
                            @endif
                        </div>
                        <div class="font-semibold text-sm">{{ $child->name }}</div>
                        <div class="text-xs text-text-muted mt-1">{{ __('shop.products_count', ['count' => $child->products_count ?? $child->products()->count()]) }}</div>
                    </a>
                @endforeach
            @endforeach
        </div>
    </section>
    @endif

    {{-- In-Car Products --}}
    @if($inStockProducts->isNotEmpty())
    <section class="mb-8">
        <h2 class="text-2xl font-extrabold mb-5 flex items-center gap-2.5">
            <i class="fa-solid fa-bolt text-accent"></i> {{ __('shop.in_car') }}
        </h2>
        <div class="relative">
            <div class="flex gap-4 overflow-x-auto scroll-smooth scrollbar-hide pb-4" style="scroll-snap-type: x mandatory;">
                @foreach($inStockProducts as $product)
                    <div class="flex-none w-64" style="scroll-snap-align: start;">
                        <x-product-card :product="$product" :inStock="true" />
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- Bestsellers --}}
    <section class="mb-8">
        <h2 class="text-2xl font-extrabold mb-5 flex items-center gap-2.5">
            <i class="fa-solid fa-fire text-accent"></i> {{ __('shop.bestsellers') }}
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @forelse($hits as $product)
                <x-product-card :product="$product" :inStock="in_array($product->id, $inStockProductIds)" />
            @empty
                <div class="col-span-full text-center py-12 text-text-muted">
                    <i class="fa-solid fa-box-open text-4xl mb-3"></i>
                    <p>{{ __('shop.no_products_yet') }}</p>
                </div>
            @endforelse
        </div>
    </section>
</div>
@endsection
