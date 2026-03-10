<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-06-23
 * Time: 09:59
 */

use App\Http\Controllers\Backend\ChannelController;

Route::prefix('channels')->as('channels.')->middleware(['role:admin_channels'])->group(function () {
    Route::get('', [ChannelController::class, 'index'])->name('index');
    Route::post('', [ChannelController::class, 'store'])->name('store');
    Route::get('create', [ChannelController::class, 'create'])->name('create');
    Route::get('{channel}/edit', [ChannelController::class, 'edit'])->name('edit');
    Route::patch('{channel}', [ChannelController::class, 'update'])->name('update');
    Route::get('{channel}/delete', [ChannelController::class, 'destroy'])->name('destroy');
    Route::post('sort', [ChannelController::class, 'sort'])->name('sort');

    Route::get('home', [ChannelController::class, 'index'])->name('home');
    Route::get('discover', [ChannelController::class, 'index'])->name('discover');
    Route::get('radio', [ChannelController::class, 'index'])->name('radio');
    Route::get('community', [ChannelController::class, 'index'])->name('community');
    Route::get('trending', [ChannelController::class, 'index'])->name('trending');
    Route::get('{id}/genre', [ChannelController::class, 'index'])->name('genre');
    Route::get('{id}/station-category', [ChannelController::class, 'index'])->name('station-category');
    Route::get('{id}/podcast-category', [ChannelController::class, 'index'])->name('podcast-category');
});
