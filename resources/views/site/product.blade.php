@extends('layouts.site')

@section('title', $title)

@section('content')
<div x-data="{
    currentImage: 0,
    images: [
        'https://placehold.co/600x600/F3F4F6/1B5E20?text=Lays',
        'https://placehold.co/600x600/F3F4F6/1B5E20?text=Lays+2',
        'https://placehold.co/600x600/F3F4F6/1B5E20?text=Lays+3',
        'https://placehold.co/600x600/F3F4F6/1B5E20?text=Lays+4'
    ],
    product: {
        name: 'Lays Классические 150г',
        price: 12000,
        oldPrice: 15000,
        inCar: true,
        description: 'Классический картофельные чипсы Lays с привычным вкусом. Идеально подходят для перекуса в дороге или во время просмотра фильма.',
        features: [
            'Вес: 150 грамм',
            'Срок годности: 6 месяцев',
            'Страна производитель: Узбекистан',
            'Состав: картофель, растительное масло, соль'
        ]
    },
    
    changeImage(index) {
        this.currentImage = index;
    },
    
    buyFromDriver() {
        this.addToCart('cash_to_driver');
        window.location.href = '{{ route('checkout') }}';
    },
    
    payWithQR() {
        this.addToCart('qr_paynet');
        window.location.href = '{{ route('checkout') }}';
    },
    
    orderToHome() {
        this.addToCart('delivery_cod');
        window.location.href = '{{ route('checkout') }}';
    },
    
    addToCart(paymentMethod) {
        $store.cart.add({
            ...this.product,
            quantity: 1,
            paymentMethod: paymentMethod
        });
        cartCount++;
    }
}">
    
    <!-- Product Layout -->
    <div class="grid lg:grid-cols-[60%_40%] gap-8 mt-5">
        
        <!-- Left Column - Images -->
        <div>
            <!-- Main Image -->
            <div class="bg-white rounded-2xl p-4 mb-4">
                <img :src="images[currentImage]" 
                     :alt="product.name"
                     class="w-full h-[400px] lg:h-[500px] object-contain rounded-xl">
            </div>
            
            <!-- Thumbnails -->
            <div class="grid grid-cols-4 gap-3">
                <template x-for="(image, index) in images" :key="index">
                    <button @click="changeImage(index)"
                            class="bg-white rounded-xl p-3 hover:shadow-lg transition-all"
                            :class="{ 'ring-2 ring-primary': currentImage === index }">
                        <img :src="image" 
                             :alt="`${product.name} ${index + 1}`"
                             class="w-full h-20 object-contain">
                    </button>
                </template>
            </div>
        </div>
        
        <!-- Right Column - Product Info -->
        <div>
            <!-- Breadcrumbs -->
            <nav class="flex items-center gap-2 text-sm text-text-muted mb-4">
                <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Главная</a>
                <i class="fa-solid fa-chevron-right text-xs"></i>
                <a href="{{ route('category', ['slug' => 'snacks']) }}" class="hover:text-primary transition-colors">Снеки</a>
                <i class="fa-solid fa-chevron-right text-xs"></i>
                <span class="text-text-main">Чипсы</span>
            </nav>
            
            <!-- Title -->
            <h1 class="text-2xl lg:text-3xl font-extrabold mb-4" x-text="product.name"></h1>
            
            <!-- Badge if in car -->
            <template x-if="product.inCar">
                <span class="inline-block bg-primary text-white text-sm font-bold px-4 py-2 rounded-full mb-4">
                    <i class="fa-solid fa-bolt mr-2"></i>Есть в машине
                </span>
            </template>
            
            <!-- Price -->
            <div class="flex items-baseline gap-3 mb-6">
                <span class="text-3xl font-extrabold text-primary" x-text="product.price.toLocaleString() + ' сум'"></span>
                <template x-if="product.oldPrice">
                    <span class="text-xl text-text-muted line-through" x-text="product.oldPrice.toLocaleString() + ' сум'"></span>
                </template>
                <template x-if="product.oldPrice">
                    <span class="bg-accent text-white text-sm font-bold px-2 py-1 rounded-lg"
                          x-text="'-' + Math.round((product.oldPrice - product.price) / product.oldPrice * 100) + '%'"></span>
                </template>
            </div>
            
            <!-- Description -->
            <div class="bg-bg-color rounded-xl p-5 mb-6">
                <h3 class="font-bold text-lg mb-3">О товаре</h3>
                <p class="text-text-muted leading-relaxed" x-text="product.description"></p>
                <ul class="mt-4 space-y-2">
                    <template x-for="feature in product.features" :key="feature">
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-check text-primary mt-1"></i>
                            <span class="text-sm" x-text="feature"></span>
                        </li>
                    </template>
                </ul>
            </div>
            
            <!-- Action Buttons -->
            <div class="space-y-3">
                <!-- Buy from Driver -->
                <button @click="buyFromDriver()"
                        :disabled="!product.inCar"
                        class="w-full bg-primary text-white font-bold py-4 px-6 rounded-xl hover:bg-primary-light transition-all active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-3">
                    <i class="fa-solid fa-hand-holding-dollar text-xl"></i>
                    <span>Купить у водителя</span>
                </button>
                
                <!-- Pay with QR -->
                <button @click="payWithQR()"
                        class="w-full bg-blue text-white font-bold py-4 px-6 rounded-xl hover:bg-blue/90 transition-all active:scale-[0.98] flex items-center justify-center gap-3">
                    <i class="fa-solid fa-qrcode text-xl"></i>
                    <span>Оплатить через QR (Paynet)</span>
                </button>
                
                <!-- Order to Home -->
                <button @click="orderToHome()"
                        class="w-full bg-accent text-white font-bold py-4 px-6 rounded-xl hover:bg-accent/90 transition-all active:scale-[0.98] flex items-center justify-center gap-3">
                    <i class="fa-solid fa-truck text-xl"></i>
                    <span>Заказать на дом</span>
                </button>
            </div>
            
            <!-- Info Note -->
            <div class="mt-6 p-4 bg-blue/10 rounded-xl">
                <p class="text-sm text-blue flex items-start gap-2">
                    <i class="fa-solid fa-info-circle mt-0.5"></i>
                    <span>При заказе на дом доставка займет 30-60 минут. Оплата при получении.</span>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
