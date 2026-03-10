<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-06-23
 * Time: 09:56
 */

/*
 * Edit Artist
*/

use App\Http\Controllers\Backend\ArtistController;
use App\Http\Controllers\Backend\ArtistRequestController;

Route::prefix('artists')->as('artists.')->middleware(['role:admin_artists'])->group(function () {
    Route::get('', [ArtistController::class, 'index'])->name('index');
    Route::get('create', [ArtistController::class, 'create'])->name('create');
    Route::post('', [ArtistController::class, 'store'])->name('store');
    Route::get('{artist}/edit', [ArtistController::class, 'edit'])->name('edit');
    Route::patch('{artist}', [ArtistController::class, 'update'])->name('update');
    Route::get('{artist}/delete', [ArtistController::class, 'destroy'])->name('delete');
    Route::get('{artist}/upload', [ArtistController::class, 'upload'])->name('upload');
    Route::post('batch', [ArtistController::class, 'batch'])->name('batch');
});

Route::prefix('artist-access')->as('artist.access.')->middleware(['role:admin_artist_claim'])->group(function () {
    Route::get('', [ArtistRequestController::class, 'index'])->name('index');
    Route::get('{artistRequest}/edit', [ArtistRequestController::class, 'edit'])->name('edit');
    Route::post('{artistRequest}', [ArtistRequestController::class, 'update'])->name('update');
    Route::get('{artistRequest}/reject', [ArtistRequestController::class, 'reject'])->name('reject');
});
