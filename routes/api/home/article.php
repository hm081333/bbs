<?php

use Illuminate\Support\Facades\Route;

Route::prefix('')->group(function () {
    Route::post('page', [\App\Http\Controllers\Home\Article\IndexController::class, 'page'])->name('page')
        ->withoutMiddleware('auth:user');
    Route::post('info', [\App\Http\Controllers\Home\Article\IndexController::class, 'info'])->name('info')
        ->withoutMiddleware('auth:user');
});

Route::prefix('category')->group(function () {
    Route::post('page', [\App\Http\Controllers\Home\Article\CategoryController::class, 'page'])->name('page')
        ->withoutMiddleware('auth:user');
    Route::post('info', [\App\Http\Controllers\Home\Article\CategoryController::class, 'info'])->name('info')
        ->withoutMiddleware('auth:user');
});

