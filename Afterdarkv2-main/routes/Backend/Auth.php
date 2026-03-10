<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-06-18
 * Time: 13:09
 */

use App\Http\Controllers\Backend\AdminAuthController;

Route::prefix('auth')->middleware(['role:admin_songs'])->group(function () {
    Route::post('upload/bulk', [AdminAuthController::class, 'upload'])->name('upload.bulk');
    Route::post('artist/{artistId}/upload', [AdminAuthController::class, 'upload'])->name('artist.upload.bulk');
    Route::post('album/{album_id}/upload', [AdminAuthController::class, 'upload'])->name('album.upload.bulk');
    Route::post('podcast/{podcast_id}/upload', [AdminAuthController::class, 'uploadEpisode'])->name('podcast.upload.bulk');
    Route::post('song', [AdminAuthController::class, 'editSong'])->name('ajax.song.edit');
    Route::post('podcast/episode', [AdminAuthController::class, 'editEpisode'])->name('ajax.podcast.episode.edit');
    Route::post('addSong', [AdminAuthController::class, 'addSong'])->name('ajax.add.song');
    Route::post('removeSong', [AdminAuthController::class, 'removeSong'])->name('ajax.remove.song');
});
