<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('testa', function () {
    \App\Models\Intel\IntelProductCategory::where('level', 1)->chunk(500, function (\Illuminate\Support\Collection $category_list) {
        $category_list->each(function ($category) {
            \App\Models\Intel\IntelProductSeries::where('category_id', $category->id)->update([
                'category_panel_key' => $category['panel_key'],
            ]);
        });
    });
})->purpose('cesi');
