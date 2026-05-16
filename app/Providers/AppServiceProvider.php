<?php

namespace App\Providers;

use App\Models\PenyimpananGabah;
use App\Models\InstruksiPenyimpanan;
use App\Observers\PenyimpananGabahObserver;
use App\Observers\InstruksiPenyimpananObserver;
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
        // Register Model Observers
        PenyimpananGabah::observe(PenyimpananGabahObserver::class);
        InstruksiPenyimpanan::observe(InstruksiPenyimpananObserver::class);
    }
}
