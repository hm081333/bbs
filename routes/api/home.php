<?php

use Illuminate\Support\Facades\Route;

Route::prefix('adv')->name('adv.')->group(function () {
    Route::any('list', [\App\Http\Controllers\Home\AdvController::class, 'list'])->name('list')
        ->withoutMiddleware('auth:user');
});

Route::prefix('intel')
    ->name('intel.')
    ->group(base_path('routes/api/home/intel.php'));

Route::prefix('article')
    ->name('article.')
    ->group(base_path('routes/api/home/article.php'));

Route::prefix('fund')
    ->name('fund.')
    ->group(base_path('routes/api/home/fund.php'));

Route::prefix('tieba')
    ->name('tieba.')
    ->group(base_path('routes/api/home/tieba.php'));

Route::prefix('forum')
    ->name('forum.')
    ->group(base_path('routes/api/home/forum.php'));

Route::prefix('user')
    ->name('user.')
    ->group(base_path('routes/api/home/user.php'));
