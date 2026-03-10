<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-06-23
 * Time: 10:00
 */

use App\Http\Controllers\Backend\BulkController;

Route::group(['middleware' => 'role:admin_songs'], function () {
    Route::get('bulk', [BulkController::class, 'index'])->name('bulk');
});
