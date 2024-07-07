<?php

namespace App\Providers;

use App\Models\Identifier;
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
        foreach (config('identity.enum')::cases() as $type) {
            $this->app->bind("{$type->value}.model", fn () => new Identifier(['identifier_type' => $type->value]));
        }
    }
}
