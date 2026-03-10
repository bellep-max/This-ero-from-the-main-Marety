<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-06-23
 * Time: 09:58
 */

/*
 * Edit Static Module
*/

use App\Http\Controllers\Backend\PageController;

Route::prefix('pages')->as('pages.')->middleware(['role:admin_pages'])->group(function () {
    Route::get('', [PageController::class, 'index'])->name('index');
    Route::post('', [PageController::class, 'store'])->name('store');
    Route::get('create', [PageController::class, 'create'])->name('create');
    Route::get('{page}/edit', [PageController::class, 'edit'])->name('edit');
    Route::patch('{page}', [PageController::class, 'update'])->name('update');
    Route::get('{page}/delete', [PageController::class, 'destroy'])->name('destroy');
});
