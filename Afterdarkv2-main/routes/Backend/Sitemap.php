<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-06-23
 * Time: 10:00
 */

use App\Http\Controllers\Backend\SitemapController;

Route::prefix('sitemap')->as('sitemap.')->middleware(['role:admin_sitemap'])->group(function () {
    Route::get('', [SitemapController::class, 'index'])->name('index');
    Route::post('', [SitemapController::class, 'store'])->name('store');
});
