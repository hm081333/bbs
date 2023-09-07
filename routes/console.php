<?php

use App\Jobs\FundValuationUpdateJob;
use App\Models\Fund\FundNetValue;
use App\Models\Fund\FundValuation;
use App\Utils\Tools;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

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
})->purpose('cesi');
