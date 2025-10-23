<?php

namespace App\Providers;

use App\Contracts\CreatePostInterface;
use App\Contracts\PostRetrievalInterface;
use App\Contracts\SubscribeServiceInterface;
use App\Contracts\UpdatePostInterface;
use App\Contracts\WebsiteCreationInterface;
use App\Contracts\WebsiteRetrievalInterface;
use App\Services\CreatePostService;
use App\Services\PostRetrievalService;
use App\Services\SubscribeService;
use App\Services\UpdatePostService;
use App\Services\WebsiteCreationService;
use App\Services\WebsiteRetrievalService;
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
        $this->app->bind(PostRetrievalInterface::class, PostRetrievalService::class);
        $this->app->bind(WebsiteCreationInterface::class, WebsiteCreationService::class);
        $this->app->bind(WebsiteRetrievalInterface::class, WebsiteRetrievalService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
