<?php

namespace App\Providers;

use App\Models\File;
use App\Models\View;
use App\Observers\FileObserver;
use App\Observers\ViewObserver;
use App\Services\FileService;
use App\Services\GlowService;
use App\Services\InviteService;
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
    public function register(): void
    {
        // singleton service's
        $this->app->singleton(FileService::class, FileService::class);
        $this->app->singleton(GlowService::class, GlowService::class);
        $this->app->singleton(InviteService::class, InviteService::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        // model observes
        File::observe(FileObserver::class);
        View::observe(ViewObserver::class);

        // get route by file
        Route::pattern('file', '.+'); // any

        // load model bucket by user
        Route::bind('bucket', static function ($value) {
            \abort_if(!Auth::check(), 401);
            return Auth::user()
                ->buckets()
                ->where('name', $value)
                ->firstOrFail();
        });
    }
}
