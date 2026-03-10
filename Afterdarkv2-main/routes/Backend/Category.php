<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-06-23
 * Time: 09:56
 */

use App\Http\Controllers\Backend\CategoryController;

Route::prefix('categories')->as('categories.')->middleware(['role:admin_categories'])->group(function () {
    Route::get('', [CategoryController::class, 'index'])->name('index');
    Route::post('', [CategoryController::class, 'store'])->name('store');
    Route::get('create', [CategoryController::class, 'create'])->name('create');
    Route::get('{category}/edit', [CategoryController::class, 'edit'])->name('edit');
    Route::patch('{category}', [CategoryController::class, 'update'])->name('update');
    Route::get('{category}/delete', [CategoryController::class, 'destroy'])->name('destroy');
    Route::post('sort', [CategoryController::class, 'sort'])->name('sort');
});
