<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Carbon;
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

Artisan::command('fund', function () {
    $this->comment('获取基金列表');
    $today_date = date('Y-m-d', \App\Utils\Tools::time());
    $curl = \App\Utils\Tools::curl(5);
    $types = [
        'gp' => '股票型',
        'hh' => '混合型',
        'zq' => '债券型',
        'zs' => '指数型',
        'qdii' => 'QDII',
        'lof' => 'LOF',
        'fof' => 'FOF',
    ];
    foreach ($types as $key => $name) {
        $this->comment("获取{$name}基金列表");
        $allPages = 1;
        for ($page = 1; $page <= $allPages; $page++) {
            $res = $curl
                ->setHeader([
                    'Referer' => 'https://fund.eastmoney.com/data/fundranking.html',
                    'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36',
                ])
                ->get("https://fund.eastmoney.com/data/rankhandler.aspx?op=ph&dt=kf&ft={$key}&rs=&gs=0&sc=dm&st=asc&sd={$today_date}&ed={$today_date}&pi={$page}&pn=200&dx=0");
            //dd($res);
            if ($allPages == 1) {
                preg_match('/allPages:([^,]+),/', $res, $matches);
                $allPages = (int)$matches[1];
            }
            preg_match('/datas:(\[[^]]*])/', $res, $matches);
            $data_json = $matches[1];
            $data_arr = \App\Utils\Tools::json_decode($data_json);
            $data_arr = array_map(function ($str) {
                return explode(',', $str);
            }, $data_arr);
            foreach ($data_arr as $item) {
                $this->comment("写入基金：{$item[0]}-{$item[1]}");
                \App\Jobs\FundUpdateJob::dispatch([
                    'code' => $item[0],
                    'name' => $item[1],
                    'pinyin_initial' => $item[2],
                    'type' => $key,
                ]);
                if ($item[3]) {
                    $this->comment("写入基金净值：{$item[0]}-{$item[1]}，单位净值：{$item[4]}，累计净值：{$item[5]}");
                    \App\Jobs\FundNetValueUpdateJob::dispatch([
                        'net_value_time' => $item[3],
                        'code' => $item[0],
                        'unit_net_value' => $item[4],// 单位净值
                        'cumulative_net_value' => $item[5],// 累计净值
                    ]);
                }
                //dd($fund_net_value);
            }
            //dd($data_arr[0]);
            sleep(1);
        }
    }

})->purpose('Catch Fund List');

Artisan::command('fund_valuation', function () {
    $valuation_source = 'https://www.dayfund.cn/prevalue.html';
    $table_headers = [
        '序号',
        '基金代码',
        '基金名称',
        '上期单位净值',
        '最新预估净值',
        '预估增长值',
        '预估增长率',
        '估值时间',
        '链接',
    ];
    //$funds_values = [];
    $curl = \App\Utils\Tools::curl(5);
    $max_page = 1;
    for ($current_page = 1; $current_page <= $max_page; $current_page++) {
        $page_content = $curl
            ->setHeader([
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36',
            ])
            ->get("https://www.dayfund.cn/prevalue_{$current_page}.html");
        $page_content = str_replace(["\r", "\n", "\r\n", "<br/>"], '', $page_content);
        //region 匹配页码
        preg_match_all('/prevalue_(\d+).html/', $page_content, $matches);
        $max_page = (int)max($matches[1]);
        //endregion
        //region 匹配表格内容
        preg_match('/<table>.*?<\/table>/', $page_content, $matches);
        //dd($matches);
        $table = $matches[0];
        preg_match_all('/<tr[^>]*>(.*?)<\/tr>/', $table, $matches);
        $tds = $matches[1];
        foreach ($tds as $index => $td) {
            preg_match_all('/<td[^>]*>(.*?)<\/td>/', $td, $matches);
            $values = array_map(function ($value) {
                return strip_tags($value);
            }, $matches[1]);
            if ($index == 0 && $current_page == 1) {
                $table_headers = $values;
                continue;
            }
            if (count($values) == count($table_headers)) {
                $funds_value = array_combine($table_headers, $values);
                //dd($funds_value);
                $this->comment("写入基金估值：{$funds_value['基金代码']}-{$funds_value['基金名称']}-最新预估净值：{$funds_value['最新预估净值']}");
                \App\Jobs\FundValuationUpdateJob::dispatch([
                    'code' => $funds_value['基金代码'],
                    'valuation_time' => $funds_value['估值时间'],
                    'valuation_source' => $valuation_source,
                    'estimated_net_value' => $funds_value['最新预估净值'],
                ]);
            }
        }
        //endregion
        sleep(1);
    }
})->purpose('Catch Fund Pre Value');
