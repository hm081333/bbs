<?php

use App\Exceptions\Server\InternalServerErrorException;
use App\Jobs\FundValuationUpdateJob;
use App\Models\Fund\Fund;
use App\Models\Fund\FundNetValue;
use App\Models\Fund\FundValuation;
use App\Utils\Tools;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

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
    $data = \App\Utils\Juhe\Calendar::getYearMonthHolidayList('2024-10');
    dd($data);

    $list = Fund::where('net_value_time', 0)->get()
        ->each(function ($fund) {
            dump($fund->code);
            /* @var $fundNetValue FundNetValue */
            $fundNetValue = FundNetValue::where('code', $fund->code)
                ->orderByDesc('net_value_time')
                ->first();
            if ($fundNetValue) {
                $fund->net_value_time = $fundNetValue->net_value_time;// 净值更新时间
                $fund->unit_net_value = $fundNetValue->unit_net_value;// 单位净值
                $fund->cumulative_net_value = $fundNetValue->cumulative_net_value;// 累计净值
                $fund->save();
            }
        });
    dd('end');

    $res = \App\Utils\Tools::curl(5)
        ->setHeader([
            'Referer' => 'https://fund.eastmoney.com/data/fundranking.html',
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36',
        ])
        ->get("https://fund.eastmoney.com/data/rankhandler.aspx?op=ph&dt=kf&ft=lof&rs=&gs=0&sc=dm&st=asc&sd=2023-09-05&ed=2023-09-05&pi=1&pn=200&dx=0");
    dd($res);
})->purpose('cesi');
