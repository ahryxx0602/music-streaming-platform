<?php

use Illuminate\Support\Facades\Route;
use Modules\Playlist\Http\Controllers\PlaylistController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('playlists', PlaylistController::class)->names('playlist');
});
