<?php

namespace App\Providers;

use App\Models\Absensi;
use App\Observers\AbsensiObserver;
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
        // Set locale Carbon ke bahasa Indonesia
        
        // Register observers
        Absensi::observe(AbsensiObserver::class);
    }
}
