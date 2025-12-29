<?php

namespace App\Providers;

use App\Models\Drug;
use App\Models\HealthArticle;
use App\Models\User;
use App\Observers\DrugObserver;
use App\Observers\HealthArticleObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;

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
        // Register Model Observers for cache invalidation
        Drug::observe(DrugObserver::class);
        HealthArticle::observe(HealthArticleObserver::class);
        User::observe(UserObserver::class);
    }
}
