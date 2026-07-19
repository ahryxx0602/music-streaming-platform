<?php

use Illuminate\Support\Facades\Route;
use Modules\Playlist\Http\Controllers\PlaylistController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('playlists', PlaylistController::class)->names('playlist');

    // Admin Routes
    Route::prefix('admin')->group(function () {
        Route::apiResource('playlists', \Modules\Playlist\Http\Controllers\AdminPlaylistController::class)->names('admin.playlists');
    });
});
