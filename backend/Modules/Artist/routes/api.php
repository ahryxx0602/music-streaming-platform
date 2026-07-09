<?php

use Illuminate\Support\Facades\Route;
use Modules\Artist\Http\Controllers\ArtistController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('artists', ArtistController::class)->names('artist');
});
