<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-06-23
 * Time: 09:57
 */

/*
 * Edit Region
*/

use App\Http\Controllers\Backend\RegionController;

Route::prefix('regions')->as('regions.')->middleware(['role:admin_radio'])->group(function () {
    Route::get('', [RegionController::class, 'index'])->name('index');
    Route::post('', [RegionController::class, 'store'])->name('store');
    Route::get('create', [RegionController::class, 'create'])->name('create');
    Route::get('{regionVisible}/edit', [RegionController::class, 'edit'])->name('edit');
    Route::patch('{regionVisible}', [RegionController::class, 'update'])->name('update');
    Route::get('{regionVisible}/delete', [RegionController::class, 'destroy'])->name('destroy');
});
