<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

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
    $router->get('abilities', 'Api\AuthController@abilities')
        ->name('auth.abilities');

    $router->post('token', 'Api\AuthController@token')
        ->name('auth.token');
});

Route::middleware('auth:sanctum')->group(static function (Router $router) {
    $router->apiResource('bucket', 'Api\BucketController');
    $router->apiResource('bucket/{bucket}/view', 'Api\ViewController');
    $router->apiResource('bucket/{bucket}/file', 'Api\FileController');
    $router->get('bucket/{bucket}/listContents', 'Api\FileController@listContents')
        ->name('file.listContents');
    $router->post('bucket/{bucket}/invite/{file}', 'Api\FileController@invite')
        ->name('file.invite');
});
