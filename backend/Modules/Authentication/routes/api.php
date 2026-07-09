<?php

use Illuminate\Support\Facades\Route;
use Modules\Authentication\Http\Controllers\AuthController;

Route::prefix('v1')->group(function () {
    // Guest (Public) Routes
    Route::post('guest/auth/login', [AuthController::class, 'login']);
    Route::post('guest/auth/register', [AuthController::class, 'registerListener']);
    Route::get('guest/auth/artist-register/validate', [AuthController::class, 'validateArtistToken']);
    Route::post('guest/auth/artist-register', [AuthController::class, 'registerArtist']);
    Route::post('admin/auth/login', [AuthController::class, 'login']);

    // Protected Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('{role}/auth')->whereIn('role', ['listener', 'artist', 'admin'])->group(function () {
            Route::get('me', [AuthController::class, 'me']);
            Route::post('logout', [AuthController::class, 'logout']);
        });
    });
});
