<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-06-23
 * Time: 09:58
 */

/*
 * Edit Album
*/

use App\Http\Controllers\Backend\BannerController;

Route::prefix('banners')->as('banners.')->middleware(['role:admin_banners'])->group(function () {
    Route::get('', [BannerController::class, 'index'])->name('index');
    Route::post('', [BannerController::class, 'store'])->name('store');
    Route::get('create', [BannerController::class, 'create'])->name('create');
    Route::get('{banner}/edit', [BannerController::class, 'edit'])->name('edit');
    Route::patch('{banner}', [BannerController::class, 'update'])->name('update');
    Route::get('{banner}/delete', [BannerController::class, 'delete'])->name('delete');
    Route::get('{banner}/disable', [BannerController::class, 'disable'])->name('disable');
});
