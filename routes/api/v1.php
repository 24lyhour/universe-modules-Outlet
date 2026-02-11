<?php

use Illuminate\Support\Facades\Route;
use Modules\Outlet\Http\Controllers\Api\V1\OutletApiController;

// Outlet CRUD
Route::apiResource('outlets', OutletApiController::class)
    ->names('outlet');

// Outlet Stats & Search
Route::get('outlets-stats', [OutletApiController::class, 'stats'])
    ->name('outlet.stats');
Route::get('outlets-search', [OutletApiController::class, 'search'])
    ->name('outlet.search');

// Outlet Status Actions
Route::prefix('outlets/{outlet}')->name('outlet.')->group(function () {
    Route::patch('activate', [OutletApiController::class, 'activate'])
        ->name('activate');
    Route::patch('deactivate', [OutletApiController::class, 'deactivate'])
        ->name('deactivate');
});
