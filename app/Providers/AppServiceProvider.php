<?php

namespace App\Providers;

use App\Contracts\CreatePostInterface;
use App\Contracts\SubscribeServiceInterface;
use App\Contracts\UpdatePostInterface;
use App\Services\CreatePostService;
use App\Services\SubscribeService;
use App\Services\UpdatePostService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SubscribeServiceInterface::class, SubscribeService::class);
        $this->app->bind(CreatePostInterface::class, CreatePostService::class);
        $this->app->bind(UpdatePostInterface::class, UpdatePostService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
