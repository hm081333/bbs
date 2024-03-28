<?php

use Illuminate\Support\Facades\Route;

Route::prefix('baiduid')->name('baiduid.')->group(function () {
    Route::post('page', [\App\Http\Controllers\Tieba\BaiduIdController::class, 'page'])->name('page');
    Route::post('store', [\App\Http\Controllers\Tieba\BaiduIdController::class, 'store'])->name('store');
    Route::post('getLoginQrcode', [\App\Http\Controllers\Tieba\BaiduIdController::class, 'getLoginQrcode'])->name('getLoginQrcode');
    Route::post('qrLogin', [\App\Http\Controllers\Tieba\BaiduIdController::class, 'qrLogin'])->name('qrLogin');
});

Route::prefix('tieba')->name('tieba.')->group(function () {
    Route::post('page', [\App\Http\Controllers\Tieba\TiebaController::class, 'page'])->name('page');
    Route::post('refreshTieBa', [\App\Http\Controllers\Tieba\TiebaController::class, 'refreshTieBa'])->name('refreshTieBa');
    Route::post('doSignByTieBaId', [\App\Http\Controllers\Tieba\TiebaController::class, 'doSignByTieBaId'])->name('doSignByTieBaId');
    Route::post('doSignByBaiDuId', [\App\Http\Controllers\Tieba\TiebaController::class, 'doSignByBaiDuId'])->name('doSignByBaiDuId');
    Route::post('noSignTieBa', [\App\Http\Controllers\Tieba\TiebaController::class, 'noSignTieBa'])->name('noSignTieBa');
});
