<?php

namespace App\Providers;

use App\Core\Contracts\PersonServiceInterface;
use App\Core\Services\PersonService;
use Illuminate\Support\ServiceProvider;

class BukuWargaServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind service interfaces to implementations
        $this->app->bind(PersonServiceInterface::class, PersonService::class);

        // You can add more service bindings here as they are created
        // $this->app->bind(PropertyServiceInterface::class, PropertyService::class);
        // $this->app->bind(SubRegionServiceInterface::class, SubRegionService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
