<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Welcome;
use App\Http\Controllers\Sharing\FileController;

/**
 * Add authorization and registration with laravel/ui
 */
Route::namespace('App\Http\Controllers')->group(static function () {
    Auth::routes();
});

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [Welcome::class, 'index'])
    ->name('welcome');

Route::get('/capsule/{capsule}:{thumbs}/{file}', [FileController::class, 'available'])
    ->name('capsule.available');

Route::get('/capsule/_{capsule}/{file}', [FileController::class, 'invite'])
    ->name('capsule.invite');

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return Inertia\Inertia::render('Dashboard');
})->name('dashboard');
