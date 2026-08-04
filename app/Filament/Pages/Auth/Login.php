<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Login as BaseLogin;

class Login extends BaseLogin
{
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getLoginFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getRememberFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function getLoginFormComponent(): Component
    {
        return TextInput::make('login')
            ->label(__('filament-panels::pages/auth/login.form.email.label'))
            ->placeholder(__('filament-panels::pages/auth/login.form.email.placeholder'))
            ->required()
            ->autocomplete(false)
            ->extraAttributes([
                'autocomplete' => 'off',
                'readonly' => 'readonly',
                'onfocus' => 'this.removeAttribute(\'readonly\')',
            ])
            ->extraInputAttributes([
                'class' => 'login-field',
            ])
            ->autofocus();
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label(__('filament-panels::pages/auth/login.form.password.label'))
            ->placeholder(__('filament-panels::pages/auth/login.form.password.placeholder'))
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            ->autocomplete('new-password')
            ->extraAttributes([
                'autocomplete' => 'new-password',
            ]);
    }
}