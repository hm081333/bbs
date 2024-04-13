<?php

use Illuminate\Support\Facades\Route;

Route::prefix('ark')->name('ark.')->group(function () {
    Route::prefix('category')->name('category.')->group(function () {
        Route::post('page', [\App\Http\Controllers\Home\Intel\ProductCategoryController::class, 'page'])->name('page');
    });
    Route::prefix('series')->name('series.')->group(function () {
        Route::post('page', [\App\Http\Controllers\Home\Intel\ProductSeriesController::class, 'page'])->name('page');
    });
    Route::prefix('product')->name('product.')->group(function () {
        Route::post('page', [\App\Http\Controllers\Home\Intel\ProductController::class, 'page'])->name('page');
        Route::post('info', [\App\Http\Controllers\Home\Intel\ProductController::class, 'info'])->name('info');
    });
});