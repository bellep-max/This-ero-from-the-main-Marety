<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-06-23
 * Time: 09:52
 */

use App\Http\Controllers\Frontend\ChannelController;

Route::get('channel/{id}', [ChannelController::class, 'show'])->name('channel');
