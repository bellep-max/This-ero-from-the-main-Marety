<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-06-23
 * Time: 22:56
 */

use App\Http\Controllers\Backend\ServiceController;

Route::prefix('services')->as('services.')->middleware(['role:admin_subscriptions'])->group(function () {
    Route::get('', [ServiceController::class, 'index'])->name('index');
    Route::post('', [ServiceController::class, 'store'])->name('store');
    Route::get('create', [ServiceController::class, 'create'])->name('create');
    Route::get('{service}/edit', [ServiceController::class, 'edit'])->name('edit');
    Route::patch('{service}', [ServiceController::class, 'update'])->name('update');
    Route::get('{service}/delete', [ServiceController::class, 'destroy'])->name('destroy');
});
