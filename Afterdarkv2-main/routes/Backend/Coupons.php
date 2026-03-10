<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-06-23
 * Time: 09:58
 */

/*
 * Edit Static Module
*/

use App\Http\Controllers\Backend\CouponController;

Route::prefix('coupons')->as('coupons.')->middleware(['role:admin_earnings'])->group(function () {
    Route::get('', [CouponController::class, 'index'])->name('index');
    Route::post('', [CouponController::class, 'store'])->name('store');
    Route::get('create', [CouponController::class, 'create'])->name('create');
    Route::get('{coupon}/edit', [CouponController::class, 'edit'])->name('edit');
    Route::patch('{coupon}', [CouponController::class, 'update'])->name('update');
    Route::get('{coupon}/delete', [CouponController::class, 'destroy'])->name('destroy');
});
