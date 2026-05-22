<?php

namespace App\Providers;

use App\Helpers\BreadcrumbHelper;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
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

        View::composer(
            ['layouts.welcome', 'layouts.dashboard', 'layouts.admin', 'errors.404'],
            function ($view): void {
                $data = $view->getData();
                $override = $data['cmBreadcrumbs'] ?? view()->shared('cmBreadcrumbs');
                $view->with('cmBreadcrumbs', BreadcrumbHelper::resolve(
                    is_array($override) ? $override : null
                ));
            }
        );
    }
}
