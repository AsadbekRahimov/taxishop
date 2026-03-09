<?php

declare(strict_types=1);

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use SensitiveParameter;

class Login extends BaseLogin
{
    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('login')
            ->label('Логин')
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getCredentialsFromFormData(#[SensitiveParameter] array $data): array
    {
        return [
            'login' => $data['login'],
            'password' => $data['password'],
        ];
    }
}
