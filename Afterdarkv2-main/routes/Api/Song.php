<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-06-23
 * Time: 09:53
 */

use App\Http\Controllers\Api\SongController;
use App\Http\Controllers\Frontend\StreamController;

Route::name('song.')->group(function () {
    Route::prefix('song')->group(function () {
        Route::get('{song}', [SongController::class, 'show'])->name('show');
        Route::post('autoplay', [SongController::class, 'autoplay'])->name('autoplay.get');
        Route::get('{ids}', [SongController::class, 'songFromIds'])->name('by.ids');
    });

    Route::prefix('stream')->as('stream.')->group(function () {
        Route::get('{id}', [StreamController::class, 'mp3'])->name('mp3');
        Route::get('hls/{id}', [StreamController::class, 'hls'])->name('hls');
        Route::get('hls/{id}/hd', [StreamController::class, 'hlsHD'])->name('hls.hd');
        Route::get('{id}/youtube', [StreamController::class, 'youtube'])->name('youtube');
        Route::post('on-track-played/{id}', [StreamController::class, 'onTrackPlayed'])->name('track.played');
    });
});
