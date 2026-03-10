<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-06-23
 * Time: 09:57
 */

use App\Http\Controllers\Backend\PostController;
use App\Http\Controllers\Backend\PostMediaController;

Route::prefix('posts')->as('posts.')->middleware(['role:admin_posts'])->group(function () {
    Route::get('', [PostController::class, 'index'])->name('index');
    Route::post('', [PostController::class, 'store'])->name('store');
    Route::get('create', [PostController::class, 'create'])->name('create');
    Route::get('{postVisible}/edit', [PostController::class, 'edit'])->name('edit');
    Route::patch('{postVisible}', [PostController::class, 'update'])->name('update');
    Route::get('{postVisible}/delete', [PostController::class, 'destroy'])->name('destroy');
    Route::post('batch', [PostController::class, 'batch'])->name('batch');

    Route::prefix('media')->as('media.')->group(function () {
        Route::get('', [PostMediaController::class, 'index'])->name('index');
        Route::get('{file:post_id}', [PostMediaController::class, 'show'])->name('associated');
        Route::post('get', [PostMediaController::class, 'get'])->name('get');
        Route::post('delete', [PostMediaController::class, 'destroy'])->name('delete');
        Route::post('download', [PostMediaController::class, 'download'])->name('download');
        Route::post('upload', [PostMediaController::class, 'upload'])->name('upload');
    });
});
