<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-08-01
 * Time: 20:34
 */

use App\Http\Controllers\Settings\AccountController;

Route::name('account.')->group(function () {
    Route::get('reset-password/{token}', [AccountController::class, 'resetPassword'])->name('reset.password');
    Route::post('reset-password', [AccountController::class, 'setNewPassword'])->name('set.new.password');
    Route::get('verify/{code}', [AccountController::class, 'verifyEmail'])->name('verify');
});
