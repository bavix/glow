<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BucketController;
use App\Http\Controllers\Api\ViewController;
use App\Http\Controllers\Api\FileController;

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
    $router->get('abilities', [AuthController::class, 'abilities'])
        ->name('auth.abilities');

    $router->post('token', [AuthController::class, 'token'])
        ->name('auth.token');
});

Route::middleware('auth:sanctum')->group(static function (Router $router) {
    $router->apiResource('bucket', BucketController::class);
    $router->apiResource('bucket/{bucket}/view', ViewController::class);
    $router->apiResource('bucket/{bucket}/file', FileController::class);
    $router->get('bucket/{bucket}/listContents', [FileController::class, 'listContents'])
        ->name('file.listContents');
    $router->post('bucket/{bucket}/invite/{file}', [FileController::class, 'invite'])
        ->name('file.invite');
});
