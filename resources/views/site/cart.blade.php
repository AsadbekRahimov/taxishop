@extends('layouts.site')

@section('title', $title)

@section('content')
<div x-data="{
    items: [
        { id: 1, name: 'Lays Классические', price: 12000, quantity: 2, image: 'lays', oldPrice: 15000 },
        { id: 2, name: 'Coca-Cola 0.5л', price: 8000, quantity: 3, image: 'cola', oldPrice: null },
        { id: 3, name: 'Snickers', price: 6000, quantity: 1, image: 'snickers', oldPrice: 7000 }
    ],
    
    get total() {
        return this.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    },
    
    get totalItems() {
        return this.items.reduce((sum, item) => sum + item.quantity, 0);
    },
    
    updateQuantity(id, quantity) {
        const item = this.items.find(i => i.id === id);
        if (item) {
            if (quantity <= 0) {
                this.removeItem(id);
            } else {
                item.quantity = quantity;
            }
        }
    },
    
    removeItem(id) {
        const index = this.items.findIndex(i => i.id === id);
        if (index > -1) {
            this.items.splice(index, 1);
        }
    },
    
    formatPrice(price) {
        return price.toLocaleString('ru-RU') + ' сум';
    }
}" class="max-w-4xl mx-auto">
    
    <h1 class="text-3xl font-extrabold mb-6 flex items-center gap-3">
        <i class="fa-solid fa-shopping-cart text-primary"></i>
        Корзина 
        <span class="text-lg font-normal text-text-muted" x-text="`(${totalItems} ${totalItems === 1 ? 'товар' : totalItems < 5 ? 'товара' : 'товаров'})`"></span>
    </h1>
    
    <!-- Cart Items -->
    <template x-if="items.length > 0">
        <div class="space-y-4 mb-8">
            <template x-for="item in items" :key="item.id">
                <div class="bg-white rounded-2xl p-5 shadow-sm hover:shadow-md transition-all"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform translate-x-20"
                     x-transition:enter-end="opacity-100 transform translate-x-0">
                    
                    <div class="flex items-center gap-4">
                        <!-- Product Image -->
                        <img :src="`https://placehold.co/100x100/F3F4F6/1B5E20?text=${item.image}`"
                             :alt="item.name"
                             class="w-20 h-20 object-cover rounded-xl">
                        
                        <!-- Product Info -->
                        <div class="flex-1">
                            <h3 class="font-bold text-lg mb-1" x-text="item.name"></h3>
                            <div class="flex items-baseline gap-2">
                                <span class="text-primary font-bold" x-text="formatPrice(item.price)"></span>
                                <template x-if="item.oldPrice">
                                    <span class="text-sm text-text-muted line-through" x-text="formatPrice(item.oldPrice)"></span>
                                </template>
                            </div>
                        </div>
                        
                        <!-- Quantity Control -->
                        <div class="flex items-center gap-2 bg-bg-color rounded-xl p-1">
                            <button @click="updateQuantity(item.id, item.quantity - 1)"
                                    class="w-8 h-8 rounded-lg bg-white flex items-center justify-center hover:bg-primary hover:text-white transition-colors">
                                <i class="fa-solid fa-minus text-xs"></i>
                            </button>
                            <input type="number" 
                                   x-model="item.quantity"
                                   @change="updateQuantity(item.id, parseInt($event.target.value) || 1)"
                                   class="w-12 text-center bg-transparent font-bold outline-none">
                            <button @click="updateQuantity(item.id, item.quantity + 1)"
                                    class="w-8 h-8 rounded-lg bg-white flex items-center justify-center hover:bg-primary hover:text-white transition-colors">
                                <i class="fa-solid fa-plus text-xs"></i>
                            </button>
                        </div>
                        
                        <!-- Item Total -->
                        <div class="text-right min-w-[100px]">
                            <div class="text-lg font-bold text-primary" x-text="formatPrice(item.price * item.quantity)"></div>
                        </div>
                        
                        <!-- Remove Button -->
                        <button @click="removeItem(item.id)"
                                class="w-10 h-10 rounded-xl bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-100 transition-colors">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </template>
    
    <!-- Empty Cart -->
    <template x-if="items.length === 0">
        <div class="text-center py-20">
            <i class="fa-solid fa-shopping-cart text-6xl text-text-muted mb-4"></i>
            <p class="text-xl text-text-muted mb-6">Корзина пуста</p>
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 bg-primary text-white font-bold py-3 px-6 rounded-xl hover:bg-primary-light transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
                Вернуться к покупкам
            </a>
        </div>
    </template>
    
    <!-- Cart Summary -->
    <template x-if="items.length > 0">
        <div class="bg-white rounded-2xl p-6 shadow-sm sticky bottom-5">
            <div class="flex justify-between items-center mb-6">
                <span class="text-xl text-text-muted">Итого:</span>
                <span class="text-3xl font-extrabold text-primary" x-text="formatPrice(total)"></span>
            </div>
            
            <div class="space-y-3">
                <a href="{{ route('checkout') }}" 
                   class="w-full bg-primary text-white font-bold py-4 px-6 rounded-xl hover:bg-primary-light transition-all active:scale-[0.98] flex items-center justify-center gap-3 text-lg">
                    <i class="fa-solid fa-credit-card"></i>
                    Оформить заказ
                </a>
                
                <a href="{{ route('home') }}" 
                   class="w-full border-2 border-border bg-white text-text-main font-bold py-4 px-6 rounded-xl hover:border-primary transition-all flex items-center justify-center gap-3">
                    <i class="fa-solid fa-arrow-left"></i>
                    Продолжить покупки
                </a>
            </div>
            
            <!-- Info -->
            <div class="mt-4 p-3 bg-bg-color rounded-xl">
                <p class="text-sm text-text-muted text-center">
                    <i class="fa-solid fa-truck mr-2"></i>
                    Доставка 30-60 минут при заказе на дом
                </p>
            </div>
        </div>
    </template>
</div>
@endsection
