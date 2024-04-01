<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HealthCheckController;
use Illuminate\Http\Request;
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

Route::get('healthcheck', [HealthCheckController::class, 'healthcheck'])->name('healthcheck')->name('healthcheck');

Route::post('login', [AuthController::class, 'login'])->name('user.login');
Route::post('register', [AuthController::class, 'register'])->name('user.register');

Route::middleware(['auth:api'])->group(function () {
    Route::get('me', [AuthController::class, 'me'])->name('user.me');
});

/*Route::fallback(function () {
    return response()->json(['success' => false, 'message' => 'Route not be found'], 404);
});*/


