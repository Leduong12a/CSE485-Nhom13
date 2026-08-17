<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;

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
        // Đăng ký Microsoft Azure Socialite driver
        $this->app['events']->listen(
            SocialiteWasCalled::class,
            \SocialiteProviders\Azure\AzureExtendSocialite::class . '@handle'
        );
    }
}
