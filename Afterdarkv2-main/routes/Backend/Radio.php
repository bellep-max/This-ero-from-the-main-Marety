<?php

/*
 * Edit Radio
*/

use App\Http\Controllers\Backend\RadioController;

Route::prefix('radios')->as('radios.')->middleware(['role:admin_radio'])->group(function () {
    Route::get('', [RadioController::class, 'index'])->name('index');
    Route::post('', [RadioController::class, 'store'])->name('store');
    Route::get('create', [RadioController::class, 'create'])->name('create');
    Route::get('{radio}/edit', [RadioController::class, 'edit'])->name('edit');
    Route::patch('{radio}', [RadioController::class, 'update'])->name('update');
    Route::get('{radio}/delete', [RadioController::class, 'destroy'])->name('destroy');
    Route::post('sort', [RadioController::class, 'sort'])->name('sort');
});
