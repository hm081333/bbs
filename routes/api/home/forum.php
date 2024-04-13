<?php

use Illuminate\Support\Facades\Route;

Route::prefix('community')->name('community.')->group(function () {
    Route::post('page', [\App\Http\Controllers\Home\Forum\CommunityController::class, 'page'])->name('page');
    Route::post('list', [\App\Http\Controllers\Home\Forum\CommunityController::class, 'list'])->name('list');
    Route::post('info', [\App\Http\Controllers\Home\Forum\CommunityController::class, 'info'])->name('info');
});

Route::prefix('topic')->name('topic.')->group(function () {
    Route::post('page', [\App\Http\Controllers\Home\Forum\TopicController::class, 'page'])->name('page');
    Route::post('add', [\App\Http\Controllers\Home\Forum\TopicController::class, 'add'])->name('add')->middleware('auth:user');
    Route::post('info', [\App\Http\Controllers\Home\Forum\TopicController::class, 'info'])->name('info');
});

Route::prefix('reply')->name('reply.')->group(function () {
    Route::post('page', [\App\Http\Controllers\Home\Forum\ReplyController::class, 'page'])->name('page');
    Route::post('add', [\App\Http\Controllers\Home\Forum\ReplyController::class, 'add'])->name('add')->middleware('auth:user');
});
