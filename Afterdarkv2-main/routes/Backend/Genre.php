<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-06-23
 * Time: 09:56
 */

/*
 * Edit Genre
*/

use App\Http\Controllers\Backend\GenreController;

Route::prefix('genres')->as('genres.')->middleware(['role:admin_genres'])->group(function () {
    Route::get('', [GenreController::class, 'index'])->name('index');
    Route::post('', [GenreController::class, 'store'])->name('store');
    Route::get('create', [GenreController::class, 'create'])->name('create');
    Route::get('{genre}/edit', [GenreController::class, 'edit'])->name('edit');
    Route::patch('{genre}', [GenreController::class, 'update'])->name('update');
    Route::get('{genre}/delete', [GenreController::class, 'destroy'])->name('destroy');
    Route::post('sort', [GenreController::class, 'sort'])->name('sort');
});
