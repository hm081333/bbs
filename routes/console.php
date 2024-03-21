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
    \App\Models\Fund\FundProduct::chunk(500, function (\Illuminate\Support\Collection $list) {
        $list->each(function ($item) {
            dd(\Overtrue\Pinyin\Pinyin::abbr($item['name'])->join(''));
        });
    });
})->purpose('cesi');
