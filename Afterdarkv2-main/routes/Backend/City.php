<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-06-23
 * Time: 09:57
 */

/*
 * Edit Radio
*/

use App\Http\Controllers\Backend\CityController;

Route::prefix('cities')->as('cities.')->middleware(['role:admin_radio'])->group(function () {
    Route::get('', [CityController::class, 'index'])->name('index');
    Route::post('', [CityController::class, 'store'])->name('store');
    Route::get('create', [CityController::class, 'create'])->name('create');
    Route::get('{city}/edit', [CityController::class, 'edit'])->name('edit');
    Route::patch('{city}', [CityController::class, 'update'])->name('update');
    Route::get('{city}/delete', [CityController::class, 'destroy'])->name('destroy');
});
