<?php

namespace App\Providers;

use App\Models\Bucket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        Route::bind('bucket', static function ($value) {
            \abort_if(!Auth::check(), 401);
            return Auth::user()
                ->buckets()
                ->where('name', $value)
                ->firstOrFail();
        });
    }
}
