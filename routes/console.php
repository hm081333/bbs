<?php

use App\Jobs\FundValuationUpdateJob;
use App\Models\Fund\Fund;
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
    $code = '010156';
    $fund = Fund::where('code', $code)->first();
    $allPages = 1;
    for ($page = 1; $page <= $allPages; $page++) {
        $res = \App\Utils\Tools::curl(5)
            ->setHeader([
                'Referer' => 'https://fundf10.eastmoney.com/',
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36',
            ])
            ->json_get('https://api.fund.eastmoney.com/f10/lsjz?fundCode=320007&pageIndex=1&pageSize=20', [
                'fundCode' => $code,
                'pageIndex' => 1,
                'pageSize' => 500,
            ]);
        dd($res);
        if ($allPages == 1 && $page == 1) {
            $allPages = ceil($res['TotalCount'] / $res['PageSize']);
        }
        if (!empty($res['Data']) && !empty($res['Data']['LSJZList'])) {
            $first = $res['Data']['LSJZList'][0];
            $last = $res['Data']['LSJZList'][count($res['Data']['LSJZList']) - 1];
            /* @var $isset_net_value_time_arr Illuminate\Support\Collection */
            $isset_net_value_time_arr = FundNetValue::where('code', $code)
                ->where('net_value_time', '>=', Carbon::parse($last['FSRQ'])->timestamp)
                ->where('net_value_time', '<=', Carbon::parse($first['FSRQ'])->timestamp)
                ->pluck('net_value_time')
                ->map(function ($item) {
                    return $item->format('Y-m-d');
                })
                ->toArray();
            $insert_list = [];
            foreach ($res['Data']['LSJZList'] as $item) {
                if (in_array($item['FSRQ'], $isset_net_value_time_arr)) continue;
                $insert_list[] = [
                    'fund_id' => $fund->id,
                    'code' => $fund->code,
                    'name' => $fund->name,
                    'unit_net_value' => $item['DWJZ'],// 单位净值
                    'cumulative_net_value' => $item['LJJZ'],// 累计净值
                    'net_value_time' => Carbon::parse($item['FSRQ'])->timestamp,
                    'created_at' => Carbon::parse($item['FSRQ'])->setHour(15)->timestamp,
                    'updated_at' => time(),
                ];
            }
            FundNetValue::insert($insert_list);
        }
    }
    dd(123);

    $res = \App\Utils\Tools::curl(5)
        ->setHeader([
            'Referer' => 'https://fund.eastmoney.com/data/fundranking.html',
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36',
        ])
        ->get("https://fund.eastmoney.com/data/rankhandler.aspx?op=ph&dt=kf&ft=lof&rs=&gs=0&sc=dm&st=asc&sd=2023-09-05&ed=2023-09-05&pi=1&pn=200&dx=0");
    dd($res);
})->purpose('cesi');
