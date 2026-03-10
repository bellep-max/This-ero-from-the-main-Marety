<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-06-23
 * Time: 09:57
 */

/*
 * Edit Radio
*/

use App\Http\Controllers\Backend\PodcastCategoryController;

Route::prefix('podcast-categories')->as('podcast-categories.')->middleware(['role:admin_radio'])->group(function () {
    Route::get('', [PodcastCategoryController::class, 'index'])->name('index');
    Route::post('', [PodcastCategoryController::class, 'store'])->name('store');
    Route::get('create', [PodcastCategoryController::class, 'create'])->name('create');
    Route::get('{category}/edit', [PodcastCategoryController::class, 'edit'])->name('edit');
    Route::patch('{category}', [PodcastCategoryController::class, 'update'])->name('update');
    Route::get('{category}/delete', [PodcastCategoryController::class, 'destroy'])->name('destroy');
    Route::post('sort', [PodcastCategoryController::class, 'sort'])->name('sort');
});
