<?php

namespace App\Console\Commands\Fund;

use App\Jobs\FundNetValueUpdateJob;
use App\Jobs\FundUpdateJob;
use App\Utils\Tools;
use Illuminate\Console\Command;

class FundNetValueUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fund:net-value-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '基金：净值更新';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //$this->comment('获取基金列表');
        $today_date = date('Y-m-d', Tools::time());
        $curl = Tools::curl(5);
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
            //$this->comment("获取{$name}基金列表");
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
                $data_arr = Tools::json_decode($data_json);
                $data_arr = array_map(function ($str) {
                    return explode(',', $str);
                }, $data_arr);
                foreach ($data_arr as $item) {
                    //$this->comment("写入基金：{$item[0]}-{$item[1]}");
                    FundUpdateJob::dispatch([
                        'code' => $item[0],
                        'name' => $item[1],
                        'pinyin_initial' => $item[2],
                        'type' => $key,
                    ]);
                    if ($item[3]) {
                        //$this->comment("写入基金净值：{$item[0]}-{$item[1]}，单位净值：{$item[4]}，累计净值：{$item[5]}");
                        FundNetValueUpdateJob::dispatch([
                            'net_value_time' => $item[3],
                            'code' => $item[0],
                            'unit_net_value' => $item[4],// 单位净值
                            'cumulative_net_value' => $item[5],// 累计净值
                        ]);
                    }
                }
                usleep(500);
            }
        }
        return Command::SUCCESS;
    }
}
