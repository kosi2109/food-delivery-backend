<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Http\Livewire\Auth\Login as FilamentLogin;

class Login extends FilamentLogin
{
    public function mount(): void
    {
        parent::mount();

        $this->form->schema([
            TextInput::make('email')
                ->label(__('filament::login.fields.email.label'))
                ->email()
                ->required()
                ->autocomplete(),
            TextInput::make('password')
                ->label(__('filament::login.fields.password.label'))
                ->password()
                ->required(),
            Checkbox::make('remember')
                ->label(__('filament::login.fields.remember.label')),
        ]);
    }

    protected function getViewData(): array
    {
        return [
            ...parent::getViewData(),
            'registerLink' => route('register.restaurant-admin'),
        ];
    }
}