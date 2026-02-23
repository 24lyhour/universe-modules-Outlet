<?php

use Illuminate\Support\Facades\Route;
use Modules\Outlet\Http\Controllers\Dashboard\V1\OutletDashboardController;
use Modules\Outlet\Http\Controllers\Dashboard\V1\TypeOutletDashboardController;

Route::middleware(['auth', 'verified', 'auto.permission'])->prefix('dashboard')->name('outlet.')->group(function () {
    // Outlets
    Route::resource('outlets', OutletDashboardController::class)->names('outlets');
    Route::get('outlets/{outlet}/delete', [OutletDashboardController::class, 'confirmDelete'])->name('outlets.confirm-delete');

    // Outlet Types
    Route::resource('outlet-types', TypeOutletDashboardController::class)->names('outlet-types');
    Route::get('outlet-types/{outlet_type}/delete', [TypeOutletDashboardController::class, 'confirmDelete'])->name('outlet-types.confirm-delete');
});
