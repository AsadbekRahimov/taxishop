<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход - TaxiShop</title>
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
    </style>
</head>
<body class="bg-bg-color min-h-screen flex items-center justify-center p-5">
    <div class="bg-white rounded-3xl shadow-lg w-full max-w-md p-8 fade-in-up" 
         x-data="{
             login: '',
             password: '',
             remember: true,
             errors: {},
             loading: false,
             
             validate() {
                 this.errors = {};
                 
                 if (!this.login) {
                     this.errors.login = 'Введите логин';
                 } else if (this.login.length < 3) {
                     this.errors.login = 'Минимум 3 символа';
                 }
                 
                 if (!this.password) {
                     this.errors.password = 'Введите пароль';
                 } else if (this.password.length < 6) {
                     this.errors.password = 'Минимум 6 символов';
                 }
                 
                 return Object.keys(this.errors).length === 0;
             },
             
             async submit() {
                 if (!this.validate()) return;
                 
                 this.loading = true;
                 // Имитация отправки формы
                 await new Promise(r => setTimeout(r, 1000));
                 window.location.href = '{{ route('home') }}';
             }
         }">
        
        <!-- Logo -->
        <div class="flex items-center justify-center gap-2.5 text-3xl font-extrabold text-primary mb-8">
            TaxiShop <i class="fa-solid fa-taxi text-accent"></i>
        </div>
        
        <!-- Form -->
        <form @submit.prevent="submit()">
            @csrf
            <!-- Login Field -->
            <div class="mb-5">
                <label class="block text-sm font-semibold text-text-muted mb-2">Логин</label>
                <input type="text" 
                       name="login" 
                       x-model="login"
                       class="w-full px-4 py-3 border-2 border-border rounded-xl text-lg outline-none transition-colors focus:border-primary bg-white"
                       :class="{ 'border-red-500': errors.login }"
                       placeholder="Введите логин"
                       required>
                <template x-if="errors.login">
                    <p class="text-red-500 text-sm mt-1" x-text="errors.login"></p>
                </template>
            </div>
            
            <!-- Password Field -->
            <div class="mb-5">
                <label class="block text-sm font-semibold text-text-muted mb-2">Пароль</label>
                <input type="password" 
                       name="password" 
                       x-model="password"
                       class="w-full px-4 py-3 border-2 border-border rounded-xl text-lg outline-none transition-colors focus:border-primary bg-white"
                       :class="{ 'border-red-500': errors.password }"
                       placeholder="Введите пароль"
                       required>
                <template x-if="errors.password">
                    <p class="text-red-500 text-sm mt-1" x-text="errors.password"></p>
                </template>
            </div>
            
            <!-- Remember Checkbox -->
            <div class="mb-8">
                <label class="flex items-center gap-2.5 text-base cursor-pointer">
                    <input type="checkbox" 
                           name="remember" 
                           x-model="remember"
                           class="w-6 h-6 accent-primary" checked>
                    <span>Запомнить меня</span>
                </label>
            </div>
            
            <!-- Submit Button -->
            <button type="submit" 
                    class="w-full bg-primary text-white font-bold py-4 px-6 rounded-xl text-lg transition-all active:scale-[0.98] active:bg-primary-light flex items-center justify-center gap-2.5"
                    :disabled="loading">
                <template x-if="loading">
                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </template>
                <span x-text="loading ? 'Вход...' : 'Войти в систему'"></span>
            </button>
        </form>
    </div>
</body>
</html>
