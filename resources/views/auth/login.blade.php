@extends('layouts.auth')

@section('content')
<div class="w-full max-w-md mx-auto px-4">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-extrabold text-primary flex items-center justify-center gap-2">
            TaxiShop <i class="fa-solid fa-taxi text-accent"></i>
        </h1>
        <p class="text-text-muted mt-2">{{ __('shop.login_subtitle') }}</p>
    </div>

    <div class="bg-white rounded-2xl p-8 shadow-sm">
        <form method="POST" action="{{ url('/auth/login') }}">
            @csrf

            <div class="mb-5">
                <label for="login" class="block text-sm font-semibold text-text-muted mb-2">{{ __('shop.login_label') }}</label>
                <input type="text"
                       id="login"
                       name="login"
                       value="{{ old('login') }}"
                       class="w-full px-4 py-3 border-2 border-border rounded-xl text-lg outline-none transition-colors focus:border-primary bg-white @error('login') border-red-500 @enderror"
                       placeholder="{{ __('shop.login_placeholder') }}"
                       required
                       autofocus>
                @error('login')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label for="password" class="block text-sm font-semibold text-text-muted mb-2">{{ __('shop.password') }}</label>
                <input type="password"
                       id="password"
                       name="password"
                       class="w-full px-4 py-3 border-2 border-border rounded-xl text-lg outline-none transition-colors focus:border-primary bg-white @error('password') border-red-500 @enderror"
                       placeholder="{{ __('shop.password_placeholder') }}"
                       required>
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center mb-6">
                <input type="checkbox"
                       id="remember"
                       name="remember"
                       {{ old('remember') ? 'checked' : '' }}
                       class="w-4 h-4 accent-primary rounded">
                <label for="remember" class="ml-2 text-sm text-text-muted">{{ __('shop.remember_me') }}</label>
            </div>

            <button type="submit"
                    class="w-full bg-primary text-white font-bold py-4 px-6 rounded-xl hover:bg-primary-light transition-all active:scale-[0.98] text-lg">
                <i class="fa-solid fa-sign-in-alt mr-2"></i> {{ __('shop.sign_in') }}
            </button>
        </form>
    </div>
</div>
@endsection
