<?php

use Illuminate\Support\Facades\Route;

/*Route::prefix('ark')->name('ark.')->group(function () {
    Route::prefix('category')->name('category.')->group(function () {
    });
    Route::prefix('series')->name('series.')->group(function () {
        Route::post('page', [\App\Http\Controllers\Intel\ProductSeriesController::class, 'page'])->name('page');
    });
    Route::prefix('product')->name('product.')->group(function () {
        Route::post('page', [\App\Http\Controllers\Intel\ProductController::class, 'page'])->name('page');
        Route::post('info', [\App\Http\Controllers\Intel\ProductController::class, 'info'])->name('info');
    });
});*/

Route::post('info', [\App\Http\Controllers\User\IndexController::class, 'info'])->name('info');
Route::post('login', [\App\Http\Controllers\User\IndexController::class, 'login'])->name('login');

