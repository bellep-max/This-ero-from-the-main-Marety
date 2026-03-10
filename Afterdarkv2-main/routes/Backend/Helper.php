<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-07-22
 * Time: 18:37
 */

use App\Http\Controllers\Backend\TerminalController;

Route::prefix('helpers')->middleware(['role:admin_terminal'])->group(function () {
    Route::get('terminal/artisan', [TerminalController::class, 'artisan'])->name('help.terminal.artisan');
    Route::post('terminal/artisan', [TerminalController::class, 'runArtisan'])->name('help.terminal.artisan.post');
});
