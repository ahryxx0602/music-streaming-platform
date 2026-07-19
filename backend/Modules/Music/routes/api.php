<?php

use Illuminate\Support\Facades\Route;
use Modules\Music\Http\Controllers\MusicController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('music', MusicController::class)->names('music');
    
    // Admin Routes
    Route::prefix('admin')->group(function () {
        Route::apiResource('genres', \Modules\Music\Http\Controllers\AdminGenreController::class)->names('admin.genres');
        
        Route::post('songs/presigned-url', [\Modules\Music\Http\Controllers\AdminSongController::class, 'generatePresignedUrl']);
        Route::apiResource('songs', \Modules\Music\Http\Controllers\AdminSongController::class)->names('admin.songs');
    });
});
