<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-07-21
 * Time: 13:17
 */

use App\Http\Controllers\Backend\MetaTagController;

Route::prefix('metatags')->as('metatags.')->middleware(['role:admin_metatags'])->group(function () {
    Route::get('', [MetaTagController::class, 'index'])->name('index');
    Route::post('', [MetaTagController::class, 'store'])->name('store');
    Route::get('{metaTag}/edit', [MetaTagController::class, 'edit'])->name('edit');
    Route::post('{metaTag}', [MetaTagController::class, 'update'])->name('update');
    Route::get('{metaTag}/delete', [MetaTagController::class, 'destroy'])->name('destroy');
    Route::post('sort', [MetaTagController::class, 'sort'])->name('sort');
});
