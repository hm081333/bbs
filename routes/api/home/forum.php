<?php

use Illuminate\Support\Facades\Route;

Route::prefix('community')->name('community.')->group(function () {
    Route::post('page', [\App\Http\Controllers\Home\Forum\CommunityController::class, 'page'])->name('page');
    Route::post('list', [\App\Http\Controllers\Home\Forum\CommunityController::class, 'list'])->name('list');
});

Route::prefix('topic')->name('topic.')->group(function () {
    Route::post('page', [\App\Http\Controllers\Home\Forum\TopicController::class, 'page'])->name('page');
});
