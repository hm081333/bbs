<?php

use Illuminate\Support\Facades\Route;

Route::prefix('baiduid')->name('baiduid.')->group(function () {
    Route::post('page', [\App\Http\Controllers\Home\Tieba\BaiduIdController::class, 'page'])->name('page');
    Route::post('list', [\App\Http\Controllers\Home\Tieba\BaiduIdController::class, 'list'])->name('list');
    Route::post('store', [\App\Http\Controllers\Home\Tieba\BaiduIdController::class, 'store'])->name('store');
    Route::post('del', [\App\Http\Controllers\Home\Tieba\BaiduIdController::class, 'del'])->name('del');
    Route::post('getLoginQrcode', [\App\Http\Controllers\Home\Tieba\BaiduIdController::class, 'getLoginQrcode'])->name('getLoginQrcode');
    Route::post('qrLogin', [\App\Http\Controllers\Home\Tieba\BaiduIdController::class, 'qrLogin'])->name('qrLogin');
});

Route::prefix('tieba')->name('tieba.')->group(function () {
    Route::post('page', [\App\Http\Controllers\Home\Tieba\TiebaController::class, 'page'])->name('page');
    Route::post('del', [\App\Http\Controllers\Home\Tieba\TiebaController::class, 'del'])->name('del');
    Route::post('refreshTieBa', [\App\Http\Controllers\Home\Tieba\TiebaController::class, 'refreshTieBa'])->name('refreshTieBa');
    Route::post('doSignByTieBaId', [\App\Http\Controllers\Home\Tieba\TiebaController::class, 'doSignByTieBaId'])->name('doSignByTieBaId');
    Route::post('doSignByBaiDuId', [\App\Http\Controllers\Home\Tieba\TiebaController::class, 'doSignByBaiDuId'])->name('doSignByBaiDuId');
    Route::post('noSignTieBa', [\App\Http\Controllers\Home\Tieba\TiebaController::class, 'noSignTieBa'])->name('noSignTieBa');
});
