<?php

namespace App\Providers;

use App\Contracts\Contract\SubscribeServiceInterface;
use App\Contracts\Contract\CreatePostInterface;
use App\Contracts\Contract\UpdatePostInterface;
use App\Services\SubscribeService;
use App\Services\CreatePostService;
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
