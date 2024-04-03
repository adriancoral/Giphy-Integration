<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\GiphyController;
use App\Http\Controllers\HealthCheckController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('healthcheck', [HealthCheckController::class, 'healthcheck'])
    ->name('healthcheck');

Route::middleware(['request.history'])->prefix('user')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('user.login');
    Route::post('register', [AuthController::class, 'register'])->name('user.register');
});

Route::middleware(['auth:api', 'request.history'])->group(function () {
    Route::group(['prefix' => 'user'], function () {
        Route::get('me', [AuthController::class, 'me'])->name('user.me');
        Route::post('logout', [AuthController::class, 'logout'])->name('user.logout');
    });

    Route::group(['prefix' => 'giphy'], function () {
        Route::post('search', [GiphyController::class, 'search'])->name('giphy.search');
        Route::post('gifs', [GiphyController::class, 'gifs'])->name('giphy.gifs');
    });

    Route::group(['prefix' => 'favorite'], function () {
        Route::post('add', [FavoriteController::class, 'add'])->name('favorite.add');
        Route::get('index', [FavoriteController::class, 'index'])->name('favorite.index');
    });
});

