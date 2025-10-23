<?php

namespace App\Providers;

use App\Contracts\SubscribeServiceInterface;
use App\Services\SubscribeService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SubscribeServiceInterface::class, SubscribeService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
