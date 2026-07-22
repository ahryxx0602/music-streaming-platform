<?php

use Illuminate\Support\Facades\Route;
use Modules\Administration\Http\Controllers\AdministrationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('administrations', AdministrationController::class)->names('administration');

    Route::prefix('admin/banners')->group(function () {
        Route::get('/', [\Modules\Administration\Http\Controllers\AdminBannerController::class, 'index']);
        Route::post('/', [\Modules\Administration\Http\Controllers\AdminBannerController::class, 'store']);
        Route::put('/reorder', [\Modules\Administration\Http\Controllers\AdminBannerController::class, 'reorder']);
        Route::post('/{id}', [\Modules\Administration\Http\Controllers\AdminBannerController::class, 'update']);
        Route::delete('/{id}', [\Modules\Administration\Http\Controllers\AdminBannerController::class, 'destroy']);
    });

    Route::prefix('admin/artist-invites')->group(function () {
        Route::get('/', [\Modules\Administration\Http\Controllers\AdminArtistInviteController::class, 'index']);
        Route::post('/', [\Modules\Administration\Http\Controllers\AdminArtistInviteController::class, 'store']);
        Route::delete('/{id}', [\Modules\Administration\Http\Controllers\AdminArtistInviteController::class, 'destroy']);
    });

    Route::prefix('admin/audit-logs')->middleware(['role:admin|super-admin'])->group(function () {
        Route::get('/', [\Modules\Administration\Http\Controllers\AdminAuditLogController::class, 'index']);
    });
    
    Route::prefix('admin/settings')->group(function () {
        Route::get('/', [\Modules\Administration\Http\Controllers\AdminSettingController::class, 'index']);
        Route::put('/', [\Modules\Administration\Http\Controllers\AdminSettingController::class, 'update']);
    });
    
    Route::get('admin/dashboard/recent-activities', [\Modules\Administration\Http\Controllers\AdminAuditLogController::class, 'recent']);

    Route::middleware(['role:super-admin'])->group(function () {
        Route::get('admin/permissions', [\Modules\Administration\Http\Controllers\AdminRoleController::class, 'permissions']);
        Route::prefix('admin/roles')->group(function () {
            Route::get('/', [\Modules\Administration\Http\Controllers\AdminRoleController::class, 'index']);
            Route::post('/', [\Modules\Administration\Http\Controllers\AdminRoleController::class, 'store']);
            Route::put('/{id}', [\Modules\Administration\Http\Controllers\AdminRoleController::class, 'update']);
            Route::delete('/{id}', [\Modules\Administration\Http\Controllers\AdminRoleController::class, 'destroy']);
        });
    });
});
