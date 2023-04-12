<?php

use App\Http\Controllers\OpcacheController;
use Illuminate\Support\Facades\Route;

Route::get('config', [OpcacheController::class, 'config']);
Route::get('status', [OpcacheController::class, 'status']);
Route::get('reset', [OpcacheController::class, 'reset']);
Route::get('clear', [OpcacheController::class, 'clear']);
Route::get('compile', [OpcacheController::class, 'compile']);
