<?php

use Illuminate\Support\Facades\Route;
use Modules\Outlet\Http\Controllers\Api\V1\OutletApiController;
use Modules\Outlet\Http\Controllers\Api\V1\OutletPublicController;

/*
|--------------------------------------------------------------------------
| Public Routes (no authentication required)
|--------------------------------------------------------------------------
| These routes are for mobile app to browse outlets
*/
Route::prefix('v1')->group(function () {
    // Public outlet listing
    Route::get('outlets', [OutletPublicController::class, 'index'])
        ->name('outlet.public.index');

    // Get outlet types for filtering
    Route::get('outlet-types', [OutletPublicController::class, 'types'])
        ->name('outlet.public.types');

    // Search outlets
    Route::get('outlets-search', [OutletPublicController::class, 'search'])
        ->name('outlet.public.search');

    // Featured/popular outlets
    Route::get('outlets-featured', [OutletPublicController::class, 'featured'])
        ->name('outlet.public.featured');

    // Single outlet detail
    Route::get('outlets/{uuid}', [OutletPublicController::class, 'show'])
        ->name('outlet.public.show');

    // Outlet products
    Route::get('outlets/{uuid}/products', [OutletPublicController::class, 'products'])
        ->name('outlet.public.products');
});

/*
|--------------------------------------------------------------------------
| Protected Routes (requires authentication)
|--------------------------------------------------------------------------
| These routes are for admin/dashboard API
*/
Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    // Outlet CRUD (admin)
    Route::apiResource('admin/outlets', OutletApiController::class)
        ->names('outlet.admin');

    // Outlet Stats
    Route::get('admin/outlets-stats', [OutletApiController::class, 'stats'])
        ->name('outlet.admin.stats');

    // Outlet Status Actions
    Route::prefix('admin/outlets/{outlet}')->name('outlet.admin.')->group(function () {
        Route::patch('activate', [OutletApiController::class, 'activate'])
            ->name('activate');
        Route::patch('deactivate', [OutletApiController::class, 'deactivate'])
            ->name('deactivate');
    });
});
