<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-06-23
 * Time: 09:56
 */

/*
 * Edit Song
*/

use App\Http\Controllers\Backend\CommentController;

Route::prefix('comments')->as('comments.')->middleware(['role:admin_comments'])->group(function () {
    Route::get('', [CommentController::class, 'index'])->name('index');
    Route::get('approved', [CommentController::class, 'index'])->name('approved');
    Route::get('{comment}/edit', [CommentController::class, 'edit'])->name('edit');
    Route::patch('{comment}', [CommentController::class, 'update'])->name('update');
    Route::get('{comment}/delete', [CommentController::class, 'destroy'])->name('destroy');
});
