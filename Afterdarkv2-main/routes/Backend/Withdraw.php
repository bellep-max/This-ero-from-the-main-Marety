<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-08-06
 * Time: 23:13
 */

use App\Http\Controllers\Backend\WithdrawController;

Route::prefix('withdraws')->as('withdraws.')->middleware(['role:admin_earnings'])->group(function () {
    Route::get('', [WithdrawController::class, 'index'])->name('index');
    Route::patch('{withdraw}', [WithdrawController::class, 'update'])->name('update');
});
