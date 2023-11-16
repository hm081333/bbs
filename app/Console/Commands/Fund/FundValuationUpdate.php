<?php

namespace App\Console\Commands\Fund;

use App\Jobs\FundNetValueUpdateJob;
use App\Jobs\FundUpdateJob;
use App\Jobs\FundValuationUpdateJob;
use App\Models\Fund\Fund;
use App\Utils\Juhe\Calendar;
use App\Utils\Tools;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

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
        if (
            $now_time->lt(date('Y-m-d 9:25'))
            ||
            (
                $now_time->gt(date('Y-m-d 11:35'))
                &&
                $now_time->lt(date('Y-m-d 12:55'))
            )
            ||
            $now_time->gt(date('Y-m-d 15:05'))
            ||
            $now_time->isWeekend()
            ||
            Calendar::isHoliday($now_time)
        ) {
            $this->comment('不在基金开门时间');
            return Command::SUCCESS;
        }
        $this->_dayfund();
        // $this->_eastmoney();
        return Command::SUCCESS;
    }

    /**
     * 基金速查网
     * @return void
     * @throws \Exception
     */
    private function _dayfund()
    {
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
            // region 匹配页码
            if ($max_page == 1) {
                preg_match_all('/prevalue_(\d+).html/', $page_content, $matches);
                if (!empty($matches[1])) $max_page = (int)max($matches[1]);
            }
            // endregion
            // region 匹配表格内容
            preg_match('/<table>.*?<\/table>/', $page_content, $matches);
            if (empty($matches[0])) {
                Log::debug($current_page);
                Log::debug($page_content);
                continue;
            }
            // dd($matches);
            $table = $matches[0];
            preg_match_all('/<tr[^>]*>(.*?)<\/tr>/', $table, $matches);
            if (empty($matches[1])) {
                Log::debug($current_page);
                Log::debug($page_content);
                continue;
            }
            foreach ($matches[1] as $index => $td) {
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
            // endregion
            // sleep(1);
            usleep(500);
        }
    }

    private function _eastmoney()
    {
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'https://fundgz.1234567.com.cn/js/',
            'timeout' => 10,
            'verify' => false,
        ]);
        $options = [
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A372 Safari/604.1',
                'Referer' => 'https://fund.eastmoney.com/',
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ];
        $responses = [];
        Fund::chunk(500, function (Collection $fund_list) use ($client, $options, &$responses) {
            $promises = [];
            $fund_list->each(function (Fund $fund) use ($client, $options, &$promises, &$responses) {
                $this->info($fund->code);
                $promises[$fund->code] = $client->getAsync($fund->code . '.js', $options);
            });
            $path_responses = \GuzzleHttp\Promise\Utils::settle($promises)->wait();
            $responses = array_merge($responses, $path_responses);
        });
        // $this->info('并发请求数量：' . count($promises));
        // 等待全部请求返回,如果其中一个请求失败会抛出异常
        // $responses = \GuzzleHttp\Promise\Utils::unwrap($promises);
        // 等待全部请求返回,允许某些请求失败
        // $responses = \GuzzleHttp\Promise\Utils::settle($promises)->wait();
        $this->info('并发请求响应数量：' . count($responses));
        $this->info('并发请求成功响应数量：' . count(array_filter($responses, fn($response) => $response['state'] == 'fulfilled')));
        // 处理响应
        // $responses->each(function ($response, $fund_code) {
        //     $this->info($fund_code);
        //     if ($response['state'] != 'fulfilled') $this->info($response['state']);
        //     /* @var $response_value \GuzzleHttp\Psr7\Response */
        //     /*$response_value = $response['value'];
        //     $result = $response_value->getBody()->getContents();
        //     preg_match('/{[^}]*}/', $result, $matches);
        //     if (!empty($matches)) {
        //         $data = \App\Utils\Tools::jsonDecode($matches[0]);
        //         FundValuationUpdateJob::dispatch([
        //             'code' => $data['fundcode'],
        //             'valuation_time' => $data['gztime'],
        //             'valuation_source' => 'https://fundgz.1234567.com.cn/js/{fundCode}.js',
        //             'estimated_net_value' => $data['gsz'],
        //         ]);
        //     }*/
        // });

    }

}
