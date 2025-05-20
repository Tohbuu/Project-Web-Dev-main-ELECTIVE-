<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Socialite service
        $this->app->singleton(\Laravel\Socialite\Contracts\Factory::class, function ($app) {
            return new \Laravel\Socialite\SocialiteManager($app);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
