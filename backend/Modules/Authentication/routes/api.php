<?php

use Illuminate\Support\Facades\Route;
use Modules\Authentication\Http\Controllers\AuthController;
use Modules\Authentication\Http\Controllers\EmailVerificationController;
use Modules\Authentication\Http\Controllers\PasswordResetController;

Route::prefix('v1')->group(function () {
    // Guest (Public) Routes
    Route::post('guest/auth/login', [AuthController::class, 'login']);
    Route::post('guest/auth/register', [AuthController::class, 'registerListener']);
    Route::get('guest/auth/artist-register/validate', [AuthController::class, 'validateArtistToken']);
    Route::post('guest/auth/artist-register', [AuthController::class, 'registerArtist']);
    Route::post('admin/auth/login', [AuthController::class, 'login']);

    // Email Verification Routes
    Route::get('guest/auth/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->name('verification.verify');
    Route::post('guest/auth/email/resend', [EmailVerificationController::class, 'resend']);

    // Password Reset Routes
    Route::post('guest/auth/password/forgot', [PasswordResetController::class, 'forgotPassword']);
    Route::post('guest/auth/password/reset', [PasswordResetController::class, 'resetPassword'])->name('password.reset');

    // Protected Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('{role}/auth')->whereIn('role', ['listener', 'artist', 'admin'])->group(function () {
            Route::get('me', [AuthController::class, 'me']);
            Route::post('logout', [AuthController::class, 'logout']);
            Route::put('password', [\Modules\Authentication\Http\Controllers\ChangePasswordController::class, 'update']);
        });
    });
});
