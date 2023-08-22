<?php

namespace App\Console\Commands\Fund;

use App\Jobs\FundNetValueUpdateJob;
use App\Jobs\FundUpdateJob;
use App\Jobs\FundValuationUpdateJob;
use App\Utils\Tools;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class FundValuationUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fund:valuation-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '基金：估值更新';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //$this->comment('获取基金估值列表');
        $now_time = Tools::now();
        if ($now_time->lt(date('Y-m-d 9:25')) || ($now_time->gt(date('Y-m-d 11:35')) && $now_time->lt(date('Y-m-d 12:55'))) || $now_time->gt(date('Y-m-d 15:05'))) {
            $this->comment('不在基金开门时间');
            return Command::SUCCESS;
        }
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
        $curl = Tools::curl(5);
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
                if ($index == 0) {
                    $table_headers = $values;
                    continue;
                }
                if (count($values) == count($table_headers)) {
                    $funds_value = array_combine($table_headers, $values);
                    //$this->comment("写入基金估值：{$funds_value['估值时间']}-{$funds_value['基金代码']}-{$funds_value['基金名称']}-{$funds_value['最新预估净值']}");
                    // 只写入当天的估值
                    if (Carbon::parse($funds_value['估值时间'])->isToday()) {
                        FundValuationUpdateJob::dispatch([
                            'code' => $funds_value['基金代码'],
                            'valuation_time' => $funds_value['估值时间'],
                            'valuation_source' => $valuation_source,
                            'estimated_net_value' => $funds_value['最新预估净值'],
                        ]);
                    }
                }
            }
            //endregion
            sleep(1);
        }
        return Command::SUCCESS;
    }
}
