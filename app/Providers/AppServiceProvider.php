<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Fix for "Specified key was too long" error
        Schema::defaultStringLength(191);

        // Force HTTPS in production (TLS 1.2+ handled by server)
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Registration password: 8–15 chars, one uppercase, one number, one special character
        Password::defaults(function () {
            return Password::min(8)
                ->max(15)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols();
        });
    }
}
