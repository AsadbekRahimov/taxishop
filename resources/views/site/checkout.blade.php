@extends('layouts.site')

@section('title', $title)

@section('content')
<div x-data="{
    name: '',
    phone: '',
    paymentMethod: 'cash_to_driver',
    deliveryAddress: '',
    errors: {},
    loading: false,
    
    cartItems: [
        { name: 'Lays Классические', quantity: 2, total: '24 000 сум' },
        { name: 'Coca-Cola 0.5л', quantity: 3, total: '24 000 сум' },
        { name: 'Snickers', quantity: 1, total: '6 000 сум' }
    ],
    total: '54 000 сум',
    
    validate() {
        this.errors = {};
        
        if (!this.name.trim()) {
            this.errors.name = 'Введите ваше имя';
        }
        
        if (!this.phone.trim()) {
            this.errors.phone = 'Введите номер телефона';
        } else if (!/^\+998\d{9}$/.test(this.phone.replace(/\s/g, ''))) {
            this.errors.phone = 'Неверный формат номера. Пример: +998 90 123 45 67';
        }
        
        if (!this.paymentMethod) {
            this.errors.paymentMethod = 'Выберите способ оплаты';
        }
        
        if (this.paymentMethod === 'delivery_cod' && !this.deliveryAddress.trim()) {
            this.errors.deliveryAddress = 'Введите адрес доставки';
        }
        
        return Object.keys(this.errors).length === 0;
    },
    
    async submit() {
        if (!this.validate()) return;
        
        this.loading = true;
        
        // Имитация отправки формы
        await new Promise(r => setTimeout(r, 1500));
        
        // Здесь будет реальная отправка формы
        console.log({
            name: this.name,
            phone: this.phone,
            paymentMethod: this.paymentMethod,
            deliveryAddress: this.deliveryAddress
        });
        
        // Редирект на страницу спасибо
        window.location.href = '{{ route('order.thanks', ['number' => '12345']) }}';
    }
}">
    
    <h1 class="text-3xl font-extrabold mb-8">Оформление заказа</h1>
    
    <form @submit.prevent="submit()" class="grid lg:grid-cols-[60%_40%] gap-8">
        @csrf
        
        <!-- Left Column - Form -->
        <div class="space-y-6">
            <!-- Name Field -->
            <div>
                <label class="block text-sm font-semibold text-text-muted mb-2">Ваше имя</label>
                <input type="text" 
                       name="name"
                       x-model="name"
                       class="w-full px-4 py-3 border-2 border-border rounded-xl text-lg outline-none transition-colors focus:border-primary bg-white"
                       :class="{ 'border-red-500': errors.name }"
                       placeholder="Например: Алишер">
                <template x-if="errors.name">
                    <p class="text-red-500 text-sm mt-1" x-text="errors.name"></p>
                </template>
            </div>
            
            <!-- Phone Field -->
            <div>
                <label class="block text-sm font-semibold text-text-muted mb-2">Номер телефона</label>
                <input type="tel" 
                       name="phone"
                       x-model="phone"
                       @input="phone = phone.replace(/[^\d+]/g, '').replace(/(\+998)(\d{2})(\d{3})(\d{2})(\d{2})/, '$1 $2 $3 $4 $5')"
                       class="w-full px-4 py-3 border-2 border-border rounded-xl text-lg outline-none transition-colors focus:border-primary bg-white"
                       :class="{ 'border-red-500': errors.phone }"
                       placeholder="+998 __ ___ __ __"
                       maxlength="19">
                <template x-if="errors.phone">
                    <p class="text-red-500 text-sm mt-1" x-text="errors.phone"></p>
                </template>
            </div>
            
            <!-- Payment Methods -->
            <div>
                <label class="block text-lg font-semibold text-text-main mb-4">Способ оплаты</label>
                <div class="space-y-3">
                    <!-- Cash to Driver -->
                    <label class="block bg-white border-2 border-border rounded-xl p-4 cursor-pointer hover:border-primary transition-all"
                           :class="{ 'border-primary bg-primary/5': paymentMethod === 'cash_to_driver' }">
                        <div class="flex items-center gap-4">
                            <input type="radio" 
                                   name="payment_method" 
                                   value="cash_to_driver"
                                   x-model="paymentMethod"
                                   class="w-5 h-5 accent-primary">
                            <div class="flex-1">
                                <div class="font-bold text-lg flex items-center gap-2">
                                    <i class="fa-solid fa-money-bill-wave text-primary"></i>
                                    Наличные водителю
                                </div>
                                <p class="text-sm text-text-muted mt-1">Оплата наличными при получении заказа</p>
                            </div>
                        </div>
                    </label>
                    
                    <!-- QR Paynet -->
                    <label class="block bg-white border-2 border-border rounded-xl p-4 cursor-pointer hover:border-primary transition-all"
                           :class="{ 'border-primary bg-primary/5': paymentMethod === 'qr_paynet' }">
                        <div class="flex items-center gap-4">
                            <input type="radio" 
                                   name="payment_method" 
                                   value="qr_paynet"
                                   x-model="paymentMethod"
                                   class="w-5 h-5 accent-primary">
                            <div class="flex-1">
                                <div class="font-bold text-lg flex items-center gap-2">
                                    <i class="fa-solid fa-qrcode text-blue"></i>
                                    QR-код (Paynet / Click)
                                </div>
                                <p class="text-sm text-text-muted mt-1">Оплата через мобильное приложение</p>
                            </div>
                        </div>
                    </label>
                    
                    <!-- Delivery COD -->
                    <label class="block bg-white border-2 border-border rounded-xl p-4 cursor-pointer hover:border-primary transition-all"
                           :class="{ 'border-primary bg-primary/5': paymentMethod === 'delivery_cod' }">
                        <div class="flex items-center gap-4">
                            <input type="radio" 
                                   name="payment_method" 
                                   value="delivery_cod"
                                   x-model="paymentMethod"
                                   class="w-5 h-5 accent-primary">
                            <div class="flex-1">
                                <div class="font-bold text-lg flex items-center gap-2">
                                    <i class="fa-solid fa-truck text-accent"></i>
                                    Заказать на дом
                                </div>
                                <p class="text-sm text-text-muted mt-1">Доставка 30-60 минут, оплата при получении</p>
                            </div>
                        </div>
                    </label>
                </div>
                <template x-if="errors.paymentMethod">
                    <p class="text-red-500 text-sm mt-1" x-text="errors.paymentMethod"></p>
                </template>
            </div>
            
            <!-- Delivery Address (conditional) -->
            <div x-show="paymentMethod === 'delivery_cod'" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform -translate-y-4"
                 x-transition:enter-end="opacity-100 transform translate-y-0">
                <label class="block text-sm font-semibold text-text-muted mb-2">Адрес доставки</label>
                <textarea name="delivery_address"
                          x-model="deliveryAddress"
                          rows="3"
                          class="w-full px-4 py-3 border-2 border-border rounded-xl text-lg outline-none transition-colors focus:border-primary bg-white resize-none"
                          :class="{ 'border-red-500': errors.deliveryAddress }"
                          placeholder="Укажите район, улицу, дом, квартиру..."></textarea>
                <template x-if="errors.deliveryAddress">
                    <p class="text-red-500 text-sm mt-1" x-text="errors.deliveryAddress"></p>
                </template>
            </div>
            
            <template x-if="errors.paymentMethod">
                <p class="text-red-500 text-sm mt-1" x-text="errors.paymentMethod"></p>
            </template>
        </div>
        
        <!-- Right Column - Order Summary -->
        <div class="lg:sticky lg:top-24 h-fit">
            <div class="bg-white rounded-2xl p-6 shadow-sm">
                <h3 class="text-xl font-bold mb-5">Ваш заказ</h3>
                
                <!-- Order Items -->
                <div class="space-y-3 mb-5">
                    <template x-for="item in cartItems" :key="item.name">
                        <div class="flex justify-between items-center pb-3 border-b border-border last:border-0">
                            <div>
                                <div class="font-semibold" x-text="item.name"></div>
                                <div class="text-sm text-text-muted" x-text="`x${item.quantity}`"></div>
                            </div>
                            <div class="font-bold text-primary" x-text="item.total"></div>
                        </div>
                    </template>
                </div>
                
                <!-- Total -->
                <input type="hidden" name="total" :value="total">
                <div class="flex justify-between items-center pt-5 border-t-2 border-border">
                    <span class="text-xl text-text-muted">К оплате:</span>
                    <span class="text-3xl font-extrabold text-primary" x-text="total"></span>
                </div>
                
                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full bg-primary text-white font-bold py-4 px-6 rounded-xl hover:bg-primary-light transition-all active:scale-[0.98] flex items-center justify-center gap-3 text-lg mt-6"
                        :disabled="loading">
                    <template x-if="loading">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </template>
                    <span x-text="loading ? 'Оформление...' : 'Подтвердить заказ'"></span>
                </button>
                
                <!-- Back Button -->
                <a href="{{ route('cart') }}" 
                   class="w-full border-2 border-border bg-white text-text-main font-bold py-3 px-6 rounded-xl hover:border-primary transition-all flex items-center justify-center gap-2 mt-3">
                    <i class="fa-solid fa-arrow-left"></i>
                    Вернуться в корзину
                </a>
            </div>
        </div>
    </form>
</div>
@endsection
