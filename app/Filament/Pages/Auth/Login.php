<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;

class Login extends BaseLogin
{
    public function mount(): void
    {
        parent::mount();

        if (! config('demo.enabled')) {
            return;
        }

        $this->form->fill([
            'email' => config('demo.email'),
            'password' => config('demo.password'),
            'remember' => true,
        ]);
    }

    public function getSubheading(): string | Htmlable | null
    {
        if (! config('demo.enabled')) {
            return parent::getSubheading();
        }

        return __('Demo account :email / :password is filled in for you. Demo data resets every hour.', [
            'email' => config('demo.email'),
            'password' => config('demo.password'),
        ]);
    }
}
