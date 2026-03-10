<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-06-23
 * Time: 09:57
 */

/*
 * Edit Radio
*/

use App\Http\Controllers\Backend\StationController;

Route::prefix('stations')->as('stations.')->middleware(['role:admin_radio'])->group(function () {
    Route::get('', [StationController::class, 'index'])->name('index');
    Route::post('', [StationController::class, 'store'])->name('store');
    Route::get('create', [StationController::class, 'create'])->name('create');
    Route::get('{station}/edit', [StationController::class, 'edit'])->name('edit');
    Route::patch('{station}', [StationController::class, 'update'])->name('update');
    Route::get('{station}/delete', [StationController::class, 'destroy'])->name('destroy');
});
