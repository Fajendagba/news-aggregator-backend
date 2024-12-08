<?php

namespace App\Providers;

use App\Services\NewsServices\{
    NewsAPIService,
    GuardianService,
    NYTimesService
};
use App\Services\NewsAggregatorService;
use Illuminate\Support\ServiceProvider;

class NewsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(NewsAggregatorService::class, function ($app) {
            return new NewsAggregatorService([
                $app->make(NewsAPIService::class),
                $app->make(GuardianService::class),
                $app->make(NYTimesService::class),
            ]);
        });

        // Register individual services
        $this->app->singleton(NewsAPIService::class, function ($app) {
            return new NewsAPIService();
        });

        $this->app->singleton(GuardianService::class, function ($app) {
            return new GuardianService();
        });

        $this->app->singleton(NYTimesService::class, function ($app) {
            return new NYTimesService();
        });
    }

    public function boot()
    {
        // Register scheduled task
        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Console\Commands\FetchNewsArticles::class,
            ]);
        }
    }
}
