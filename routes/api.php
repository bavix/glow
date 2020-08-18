<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\ViewController;
use App\Http\Controllers\Api\BucketController;

Auth::routes();

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('auth')->group(static function (Router $router) {
    $router->get('abilities', \App\Http\Controllers\Api\AuthController::class . '@abilities')
        ->name('auth.abilities');

    $router->post('token', \App\Http\Controllers\Api\AuthController::class . '@token')
        ->name('auth.token');
});


Route::middleware('auth:sanctum')->group(static function (Router $router) {
    $router->apiResource('bucket', BucketController::class);
    $router->apiResource('bucket/{bucket}/view', ViewController::class);
    $router->apiResource('bucket/{bucket}/file', FileController::class);
    $router->post('bucket/{bucket}/file/{file}/invite', FileController::class . '@invite')
        ->name('file.invite');
});
