<?php

use Illuminate\Support\Facades\Route;

Route::prefix('')->group(function () {
    Route::post('info', [\App\Http\Controllers\Home\User\IndexController::class, 'info'])->name('info');
    Route::post('login', [\App\Http\Controllers\Home\User\IndexController::class, 'login'])->name('login')
        ->withoutMiddleware('auth:user');
    Route::post('logout', [\App\Http\Controllers\Home\User\IndexController::class, 'logout'])->name('logout');
});

Route::prefix('feedback')->name('feedback.')->group(function () {
    Route::post('add', [\App\Http\Controllers\Home\User\FeedbackController::class, 'add'])->name('add');
});

Route::prefix('setting')->name('setting.')->group(function () {
    Route::prefix('notify')->name('notify.')->group(function () {
        Route::post('getBark', [\App\Http\Controllers\Home\User\NotifySettingController::class, 'getBark'])->name('getBark');
        Route::post('setBark', [\App\Http\Controllers\Home\User\NotifySettingController::class, 'setBark'])->name('setBark');
        Route::post('getPushPlus', [\App\Http\Controllers\Home\User\NotifySettingController::class, 'getPushPlus'])->name('getPushPlus');
        Route::post('setPushPlus', [\App\Http\Controllers\Home\User\NotifySettingController::class, 'setPushPlus'])->name('setPushPlus');
        Route::post('getDingDingBot', [\App\Http\Controllers\Home\User\NotifySettingController::class, 'getDingDingBot'])->name('getDingDingBot');
        Route::post('setDingDingBot', [\App\Http\Controllers\Home\User\NotifySettingController::class, 'setDingDingBot'])->name('setDingDingBot');
    });
});

