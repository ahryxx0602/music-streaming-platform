<?php

use Illuminate\Support\Facades\Route;
use Modules\Music\Http\Controllers\MusicController;

Route::prefix('v1')->group(function () {
    // Listener Routes (Public for now)
    Route::prefix('listener/songs')->group(function () {
        Route::get('/{id}', [\Modules\Music\Http\Controllers\ListenerSongController::class, 'show']);
        Route::post('/{id}/track-play', [\Modules\Music\Http\Controllers\ListenerSongController::class, 'trackPlay'])->middleware('throttle:1,60'); // 1 request per min
    });
});

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('music', MusicController::class)->names('music');
    
    // Artist Routes
    Route::prefix('artist')->middleware(['role:Artist|artist'])->group(function () {
        Route::post('songs/presigned-url', [\Modules\Music\Http\Controllers\ArtistSongController::class, 'generatePresignedUrl']);
        Route::post('songs', [\Modules\Music\Http\Controllers\ArtistSongController::class, 'store']);
        
        Route::apiResource('albums', \Modules\Music\Http\Controllers\ArtistAlbumController::class)->except(['create', 'edit']);
        Route::put('albums/{id}/reorder-songs', [\Modules\Music\Http\Controllers\ArtistAlbumController::class, 'reorderSongs']);
    });

    // Admin Routes
    Route::prefix('admin')->group(function () {
        Route::apiResource('genres', \Modules\Music\Http\Controllers\AdminGenreController::class)->names('admin.genres');
        
        Route::get('songs/unassigned', [\Modules\Music\Http\Controllers\AdminSongController::class, 'unassigned']);
        Route::post('songs/presigned-url', [\Modules\Music\Http\Controllers\AdminSongController::class, 'generatePresignedUrl']);
        Route::apiResource('songs', \Modules\Music\Http\Controllers\AdminSongController::class)->names('admin.songs');

        Route::apiResource('albums', \Modules\Music\Http\Controllers\AdminAlbumController::class)->names('admin.albums');

        Route::prefix('moderation')->group(function () {
            Route::get('songs', [\Modules\Music\Http\Controllers\AdminModerationController::class, 'index']);
            Route::get('songs/{id}', [\Modules\Music\Http\Controllers\AdminModerationController::class, 'show']);
            Route::put('songs/{id}/approve', [\Modules\Music\Http\Controllers\AdminModerationController::class, 'approve']);
            Route::put('songs/{id}/reject', [\Modules\Music\Http\Controllers\AdminModerationController::class, 'reject']);
        });
    });
});
