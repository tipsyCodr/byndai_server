<?php

namespace App\Providers;

use Carbon\Carbon;
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
        date_default_timezone_set('Asia/Kolkata');

        Carbon::serializeUsing(function ($carbon) {
            return $carbon->timezone('Asia/Kolkata')->format('Y-m-d H:i:s');
        });
    }
}
