<?php

use App\Http\Controllers\Home\Sign\TiebaController;

\Illuminate\Support\Facades\Route::prefix('tieba')->group(function () {
    \Illuminate\Support\Facades\Route::post('add', [TiebaController::class, 'add'])->name('sign.tieba.add');
    \Illuminate\Support\Facades\Route::post('edit', [TiebaController::class, 'edit'])->name('sign.tieba.edit');
    \Illuminate\Support\Facades\Route::post('page', [TiebaController::class, 'page'])->name('sign.tieba.page');
    \Illuminate\Support\Facades\Route::post('list', [TiebaController::class, 'list'])->name('sign.tieba.list');
    \Illuminate\Support\Facades\Route::post('info', [TiebaController::class, 'info'])->name('sign.tieba.info');
    \Illuminate\Support\Facades\Route::post('del', [TiebaController::class, 'del'])->name('sign.tieba.del');
});
