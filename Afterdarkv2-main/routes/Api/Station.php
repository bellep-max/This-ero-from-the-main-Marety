<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-08-01
 * Time: 20:35
 */

use App\Http\Controllers\Api\StationController;

Route::get('station/{station}', [StationController::class, 'index'])->name('station');
