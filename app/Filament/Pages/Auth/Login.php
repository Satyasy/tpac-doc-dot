<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;

class Login extends BaseLogin
{
    /**
     * Redirect to main login page instead of Filament login
     */
    public function mount(): void
    {
        // If user already authenticated, let parent handle it
        if (auth()->check()) {
            parent::mount();
            return;
        }

        // Redirect to main login page
        redirect('/login');
    }
}
