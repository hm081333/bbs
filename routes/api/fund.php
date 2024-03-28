<?php

use Illuminate\Support\Facades\Route;

Route::prefix('config')->name('config.')->group(function () {
    Route::post('index', [\App\Http\Controllers\Fund\ConfigController::class, 'index'])->name('index');
});

Route::prefix('product')->name('product.')->group(function () {
    Route::post('page', [\App\Http\Controllers\Fund\ProductController::class, 'page'])->name('page');
    Route::post('list', [\App\Http\Controllers\Fund\ProductController::class, 'list'])->name('list');
    Route::post('info', [\App\Http\Controllers\Fund\ProductController::class, 'info'])->name('info');
});

Route::prefix('optional')->name('optional.')->group(function () {
    Route::post('add', [\App\Http\Controllers\Fund\OptionalController::class, 'add'])->name('add');
    Route::post('page', [\App\Http\Controllers\Fund\OptionalController::class, 'page'])->name('page');
});
