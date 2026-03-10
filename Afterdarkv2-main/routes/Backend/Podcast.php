<?php

/*
 * Edit Podcast
*/

use App\Http\Controllers\Backend\PodcastController;
use App\Http\Controllers\Backend\PodcastEpisodeController;

Route::prefix('podcasts')->as('podcasts.')->middleware(['role:admin_radio'])->group(function () {
    Route::get('', [PodcastController::class, 'index'])->name('index');
    Route::post('', [PodcastController::class, 'store'])->name('store');
    Route::get('create', [PodcastController::class, 'create'])->name('create');
    Route::get('{podcast}/edit', [PodcastController::class, 'edit'])->name('edit');
    Route::patch('{podcast}', [PodcastController::class, 'update'])->name('update');
    Route::get('{podcast}/delete', [PodcastController::class, 'destroy'])->name('destroy');

    Route::name('episodes.')->group(function () {
        Route::get('{podcast}/episodes', [PodcastEpisodeController::class, 'show'])->name('show');
        Route::get('{podcast}/upload', [PodcastEpisodeController::class, 'upload'])->name('upload');
        Route::post('{podcast}/episodes', [PodcastEpisodeController::class, 'batch'])->name('batch');
    });
});
