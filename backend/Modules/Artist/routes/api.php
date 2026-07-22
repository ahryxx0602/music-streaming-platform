<?php

use Illuminate\Support\Facades\Route;
use Modules\Artist\Http\Controllers\ArtistController;
use Modules\Artist\Http\Controllers\ArtistDashboardController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('artists', ArtistController::class)->names('artist');

    Route::prefix('artist')->middleware(['role:Artist|artist'])->group(function () {
        Route::get('/profile', [\Modules\Artist\Http\Controllers\ArtistProfileController::class, 'show']);
        Route::post('/profile', [\Modules\Artist\Http\Controllers\ArtistProfileController::class, 'update']);
        
        Route::prefix('dashboard')->group(function () {
            Route::get('/stats', [ArtistDashboardController::class, 'stats']);
            Route::get('/top-tracks', [ArtistDashboardController::class, 'topTracks']);
            Route::get('/streams-chart', [ArtistDashboardController::class, 'streamsChart']);
        });
    });
});
