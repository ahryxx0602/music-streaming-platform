<?php

use Illuminate\Support\Facades\Route;
use Modules\Users\Http\Controllers\ProfileController;
use Modules\Users\Http\Controllers\UsersController;

// Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
Route::prefix('v1')->group(function () {
    Route::prefix('{role}')->whereIn('role', ['listener', 'artist'])->group(function () {
        Route::put('profile', [ProfileController::class, 'update']);
        Route::post('profile/avatar', [ProfileController::class, 'updateAvatar']);
    });
    
    // Admin routes
    Route::prefix('admin')->group(function () {
        Route::get('users/stats', [UsersController::class, 'stats']);
        Route::post('users/listener', [UsersController::class, 'storeListener']);
        Route::post('users/artist', [UsersController::class, 'storeArtist']);
        Route::post('users/staff', [UsersController::class, 'storeStaff']);
        Route::put('users/{id}/status', [UsersController::class, 'updateStatus']);
        Route::put('users/{id}/roles', [UsersController::class, 'updateRoles']);
        Route::apiResource('users', UsersController::class)->names('users');
    });
});
