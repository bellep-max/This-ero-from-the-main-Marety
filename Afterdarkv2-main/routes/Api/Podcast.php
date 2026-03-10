<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-06-23
 * Time: 09:51
 */

use App\Http\Controllers\Api\PodcastController;

Route::name('podcast.')->group(function () {
    Route::get('podcast/{podcast}', [PodcastController::class, 'show'])->name('show');
    Route::get('podcast/{podcast}/seasons', [PodcastController::class, 'seasons'])->name('seasons');
    Route::get('podcast/{podcast}/subscribers', [PodcastController::class, 'subscribers'])->name('subscribers');
    Route::get('episode/{episode}', [PodcastController::class, 'episode'])->name('episode');
});
