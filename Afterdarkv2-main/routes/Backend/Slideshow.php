<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-05-24
 * Time: 15:52
 */

use App\Http\Controllers\Backend\SlideshowController;

Route::prefix('slideshow')->as('slideshow.')->middleware(['role:admin_slideshow'])->group(function () {
    Route::get('', [SlideshowController::class, 'index'])->name('index');
    Route::post('', [SlideshowController::class, 'store'])->name('store');
    Route::get('create', [SlideshowController::class, 'create'])->name('create');
    Route::get('{slide}/edit', [SlideshowController::class, 'edit'])->name('edit');
    Route::patch('{slide}', [SlideshowController::class, 'update'])->name('update');
    Route::get('{slide}/delete', [SlideshowController::class, 'destroy'])->name('destroy');

    Route::post('sort', [SlideshowController::class, 'sort'])->name('sort');

    Route::get('home', [SlideshowController::class, 'index'])->name('home');
    Route::get('discover', [SlideshowController::class, 'index'])->name('discover');
    Route::get('radio', [SlideshowController::class, 'index'])->name('radio');
    Route::get('community', [SlideshowController::class, 'index'])->name('community');
    Route::get('trending', [SlideshowController::class, 'index'])->name('trending');
    Route::get('{id}/genre', [SlideshowController::class, 'index'])->name('genre');
    Route::get('{id}/station-category', [SlideshowController::class, 'index'])->name('station-category');
    Route::get('{id}/podcast-category', [SlideshowController::class, 'index'])->name('podcast-category');
});
