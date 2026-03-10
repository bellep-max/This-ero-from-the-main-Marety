<?php

namespace App\Providers;

use App\Contracts\TrendingInterface;
use App\Services\TrendingService;
use Illuminate\Support\ServiceProvider;

class TrendingProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(TrendingInterface::class, TrendingService::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
