<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::attempt([
            'login' => $credentials['login'],
            'password' => $credentials['password'],
            'is_active' => true,
        ], $remember)) {
            return back()
                ->withInput($request->only('login', 'remember'))
                ->withErrors(['login' => 'Неверный логин или пароль.']);
        }

        $user = Auth::user();

        if (! in_array($user->role, ['driver', 'admin'])) {
            Auth::logout();

            return back()
                ->withInput($request->only('login', 'remember'))
                ->withErrors(['login' => 'Доступ запрещён.']);
        }

        $request->session()->regenerate();

        return redirect()->intended('/');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/auth/login');
    }
}
