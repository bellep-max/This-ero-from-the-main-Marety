<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-06-23
 * Time: 09:54
 */

use App\Http\Controllers\Api\CommunityController;
use App\Http\Controllers\Api\DiscoverController;
use App\Http\Controllers\Frontend\HomepageController;
use App\Http\Controllers\Frontend\TrendingController;

Route::get('homepage', [HomepageController::class, 'index'])->name('homepage');
Route::get('community', [CommunityController::class, 'index'])->name('community');
Route::get('discover', [DiscoverController::class, 'index'])->name('discover');
Route::get('trending', [TrendingController::class, 'index'])->name('trending');
