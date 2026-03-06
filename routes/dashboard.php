<?php

use Illuminate\Support\Facades\Route;
use Modules\Outlet\Http\Controllers\Dashboard\V1\OutletDashboardController;
use Modules\Outlet\Http\Controllers\Dashboard\V1\OutletScheduleController;
use Modules\Outlet\Http\Controllers\Dashboard\V1\OutletStatusController;
use Modules\Outlet\Http\Controllers\Dashboard\V1\TypeOutletDashboardController;

Route::middleware(['auth', 'verified', 'auto.permission'])->prefix('dashboard')->name('outlet.')->group(function () {
    // Outlets - Export & Trash (must be before resource routes)
    Route::get('outlets/export', [OutletDashboardController::class, 'export'])->name('outlets.export');
    Route::get('outlets/trash', [OutletDashboardController::class, 'trash'])->name('outlets.trash');
    Route::get('outlets/bulk-delete', [OutletDashboardController::class, 'bulkDelete'])->name('outlets.bulk-delete');
    Route::delete('outlets/bulk-delete', [OutletDashboardController::class, 'processBulkDelete'])->name('outlets.process-bulk-delete');

    // Outlets - Resource
    Route::resource('outlets', OutletDashboardController::class)->names('outlets');
    Route::get('outlets/{outlet}/delete', [OutletDashboardController::class, 'confirmDelete'])->name('outlets.confirm-delete');
    Route::put('outlets/{outlet}/toggle-status', OutletStatusController::class)->name('outlets.toggle-status');
    Route::get('outlets/{outlet}/schedule', [OutletScheduleController::class, 'show'])->name('outlets.schedule');
    Route::put('outlets/{outlet}/schedule', [OutletScheduleController::class, 'update'])->name('outlets.update-schedule');

    // Outlets - Trash Operations
    Route::post('outlets/{outlet}/restore', [OutletDashboardController::class, 'restore'])->name('outlets.restore');
    Route::delete('outlets/{outlet}/force-delete', [OutletDashboardController::class, 'forceDelete'])->name('outlets.force-delete');

    // Outlet Types - Export & Trash (must be before resource routes)
    Route::get('outlet-types/export', [TypeOutletDashboardController::class, 'export'])->name('outlet-types.export');
    Route::get('outlet-types/trash', [TypeOutletDashboardController::class, 'trash'])->name('outlet-types.trash');
    Route::get('outlet-types/bulk-delete', [TypeOutletDashboardController::class, 'bulkDelete'])->name('outlet-types.bulk-delete');
    Route::delete('outlet-types/bulk-delete', [TypeOutletDashboardController::class, 'processBulkDelete'])->name('outlet-types.process-bulk-delete');

    // Outlet Types - Resource
    Route::resource('outlet-types', TypeOutletDashboardController::class)->names('outlet-types');
    Route::get('outlet-types/{outlet_type}/delete', [TypeOutletDashboardController::class, 'confirmDelete'])->name('outlet-types.confirm-delete');
    Route::put('outlet-types/{outlet_type}/toggle-status', [TypeOutletDashboardController::class, 'toggleStatus'])->name('outlet-types.toggle-status');

    // Outlet Types - Trash Operations
    Route::post('outlet-types/{id}/restore', [TypeOutletDashboardController::class, 'restore'])->name('outlet-types.restore');
    Route::delete('outlet-types/{id}/force-delete', [TypeOutletDashboardController::class, 'forceDelete'])->name('outlet-types.force-delete');
});
