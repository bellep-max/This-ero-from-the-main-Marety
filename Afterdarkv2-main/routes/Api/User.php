<?php

/**
 * User profile page
 * Namespaces Frontend
 */

use App\Http\Controllers\Api\ProfileController;

Route::prefix('user')->group(function () {
    Route::get('{user}', [ProfileController::class, 'index'])->name('user');
    Route::get('{user}/recent', [ProfileController::class, 'recent'])->name('user.recent');
    Route::get('{user}/feed', [ProfileController::class, 'feed'])->name('user.feed');
    Route::get('{user}/posts/{postId}', [ProfileController::class, 'posts'])->name('user.posts');
    Route::get('{user}/collection', [ProfileController::class, 'collection'])->name('user.collection');
    Route::get('{user}/favorites', [ProfileController::class, 'favorites'])->name('user.favorites');
    Route::get('{user}/playlists', [ProfileController::class, 'playlists'])->name('user.playlists');
    Route::get('{user}/purchased', [ProfileController::class, 'purchased'])->name('user.purchased');
    Route::get('{user}/playlists/subscribed', [ProfileController::class, 'subscribed'])->name('user.playlists.subscribed');

    Route::get('{user}/followers', [ProfileController::class, 'followers'])->name('user.followers');
    Route::get('{user}/following', [ProfileController::class, 'following'])->name('user.following');
    Route::get('{user}/notifications', [ProfileController::class, 'notifications'])->name('user.notifications');
    Route::get('{user}/now-playing', [ProfileController::class, 'now_playing'])->name('user.now_playing');
    Route::get('{user}/now-playing', [ProfileController::class, 'now_playing'])->name('user.now_playing.post');
});
