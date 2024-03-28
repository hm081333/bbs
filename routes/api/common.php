<?php

use App\Http\Controllers\Common\AdministrativeDivisionController;
use App\Http\Controllers\Common\FileController;
use App\Http\Controllers\Common\LanguageController;
use App\Http\Controllers\Common\OpcacheController;
use App\Http\Controllers\Common\OptionController;
use App\Http\Controllers\Common\SystemController;
use Illuminate\Support\Facades\Route;

Route::prefix('opcache')->name('opcache.')->group(function () {
    Route::get('config', [OpcacheController::class, 'config']);
    Route::get('status', [OpcacheController::class, 'status']);
    Route::get('reset', [OpcacheController::class, 'reset']);
    Route::get('clear', [OpcacheController::class, 'clear']);
    Route::get('compile', [OpcacheController::class, 'compile']);
});

Route::prefix('language')->name('language.')->group(function () {
    Route::any('', [LanguageController::class, 'index'])->name('index');
    Route::any('list', [LanguageController::class, 'list'])->name('list');
});

Route::prefix('option')->name('option.')->group(function () {
    Route::any('dict', [OptionController::class, 'dict'])->name('dict');
    Route::any('all', [OptionController::class, 'all'])->name('all');
    Route::post('get', [OptionController::class, 'get'])->name('get');
});

Route::prefix('upload')->name('upload.')->group(function () {
    Route::post('image', [FileController::class, 'uploadImage'])->name('image');
    Route::post('video', [FileController::class, 'uploadVideo'])->name('video');
});

Route::prefix('system')->name('system.')->group(function () {
    Route::any('config/{type?}', [SystemController::class, 'config'])->name('config');
});

Route::prefix('administrativeDivision')->name('administrativeDivision.')->group(function () {
    Route::any('', [AdministrativeDivisionController::class, 'index'])->name('index');
    Route::any('province_city_list', [AdministrativeDivisionController::class, 'province_city_list'])->name('province_city_list');
    Route::any('province_city_tree', [AdministrativeDivisionController::class, 'province_city_tree'])->name('province_city_tree');
});