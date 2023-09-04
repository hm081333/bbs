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
    $res = \App\Utils\Tools::curl(5)
        ->setHeader([
            'Referer' => 'https://fund.eastmoney.com/data/fundranking.html',
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36',
        ])
        ->get("https://fund.eastmoney.com/data/rankhandler.aspx?op=ph&dt=kf&ft=lof&rs=&gs=0&sc=dm&st=asc&sd=2023-09-05&ed=2023-09-05&pi=1&pn=200&dx=0");
    dd($res);
    $url = "https://fund.eastmoney.com/pingzhongdata/320007.js";
    $res = \App\Utils\Tools::curl(5)->setHeader([
        'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A372 Safari/604.1',
        'Referer' => 'https://fund.eastmoney.com/',
    ])->get($url);
})->purpose('cesi');
