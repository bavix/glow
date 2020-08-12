<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\ViewController;
use App\Http\Controllers\Api\BucketController;

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

Route::apiResource('bucket', BucketController::class);
Route::apiResource('bucket/{bucket}/view', ViewController::class);
Route::apiResource('bucket/{bucket}/file', FileController::class);
