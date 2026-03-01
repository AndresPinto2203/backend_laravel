<?php

namespace App\Providers;

use App\Domain\Currency\Repositories\CurrencyRepository;
use App\Domain\Product\Repositories\ProductRepository;
use App\Domain\ProductPrice\Repositories\ProductPriceRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentCurrencyRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentProductPriceRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentProductRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ProductRepository::class, EloquentProductRepository::class);
        $this->app->bind(CurrencyRepository::class, EloquentCurrencyRepository::class);
        $this->app->bind(ProductPriceRepository::class, EloquentProductPriceRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
