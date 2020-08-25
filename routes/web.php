<?php

use Illuminate\Support\Facades\Route;

Auth::routes();

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

Route::view('/', 'welcome')
    ->name('welcome');

Route::get('/capsule/{capsule}:{thumbs}/{routable}', 'Sharing\FileController@available')
    ->name('capsule.available');

Route::get('/capsule/_{capsule}/{routable}', 'Sharing\FileController@invite')
    ->name('capsule.invite');
