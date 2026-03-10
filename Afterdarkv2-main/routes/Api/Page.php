<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-06-23
 * Time: 09:53
 */

use App\Http\Controllers\Api\PageController;

Route::get('page/{page:alt_name}', [PageController::class, 'show'])->name('page');
