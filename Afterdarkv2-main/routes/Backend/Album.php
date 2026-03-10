<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-06-23
 * Time: 09:58
 */

/*
 * Edit Album
*/

use App\Http\Controllers\Backend\AlbumController;

Route::prefix('albums')->as('albums.')->middleware(['role:admin_albums'])->group(function () {
    Route::get('', [AlbumController::class, 'index'])->name('index');
    Route::post('add', [AlbumController::class, 'store'])->name('store');
    Route::get('create', [AlbumController::class, 'create'])->name('create');
    Route::get('{albumVisible}/edit', [AlbumController::class, 'edit'])->name('edit');
    Route::patch('{albumVisible}', [AlbumController::class, 'update'])->name('update');
    Route::get('{albumVisible}/delete', [AlbumController::class, 'destroy'])->name('destroy');

    Route::get('{albumVisible}/upload', [AlbumController::class, 'upload'])->name('upload');
    Route::post('{albumVisible}/reject', [AlbumController::class, 'reject'])->name('reject');
    Route::get('{albumVisible}/track-list', [AlbumController::class, 'trackList'])->name('tracklist');
    Route::post('{albumVisible}/track-list', [AlbumController::class, 'trackListMassAction'])->name('tracklist.batch');
    Route::post('batch', [AlbumController::class, 'batch'])->name('batch');
});
