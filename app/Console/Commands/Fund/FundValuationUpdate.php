<?php

namespace App\Console\Commands\Fund;

use App\Jobs\FundNetValueUpdateJob;
use App\Jobs\FundUpdateJob;
use App\Jobs\FundValuationUpdateJob;
use App\Models\Fund\Fund;
use App\Utils\Juhe\Calendar;
use App\Utils\Tools;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\Utils;
use GuzzleHttp\Psr7\Response;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;

class FundValuationUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fund:valuation-update
    {--eastmoney : 同步（天天基金网）估值}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '基金：估值更新';
    private array $client = [];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $start_time = microtime(true);
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
        if ($this->option('eastmoney')) {
            $this->_eastmoney();
        } else {
            $this->_dayfund();
        }
        $this->info('执行|' . $this->description . '|耗时：' . ((microtime(true) - $start_time) * 1000) . ' 毫秒');
        // 调起写入命令行
        $this->call('fund:valuation-write');
        return Command::SUCCESS;
    }

    /**
     * 基金速查网
     * @return void
     * @throws Exception
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
        $max_page = 1;
        for ($current_page = 1; $current_page <= $max_page; $current_page++) {
            $response = $this->single_http_get('https://www.dayfund.cn/', "prevalue_{$current_page}.html", [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 Safari/537.36',
            ], 6);
            $page_content = str_replace(["\r", "\n", "\r\n", "<br/>"], '', $response->getBody()->getContents());
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

    /**
     * 天天基金网
     * @return void
     */
    private function _eastmoney()
    {
        $try_list = Fund::select(['code'])
            ->pluck('code');
        while ($try_list->isNotEmpty()) {
            $try_list = $this->_eastmoney_request($try_list);
            $this->info('并发请求失败数量：' . $try_list->count());
        }
        // $this->info('并发请求数量：' . count($promises));
        // 等待全部请求返回,如果其中一个请求失败会抛出异常
        // $responses = \GuzzleHttp\Promise\Utils::unwrap($promises);
        // 等待全部请求返回,允许某些请求失败
        // $responses = \GuzzleHttp\Promise\Utils::settle($promises)->wait();
    }

    private function _eastmoney_request(Collection $codes)
    {
        $retry_list = collect();
        $codes->chunk(500)
            ->each(function (Collection $fund_codes) use (&$retry_list) {
                $responses = $this->multi_http_get('https://fundgz.1234567.com.cn/js/', $fund_codes->combine($fund_codes->map(fn(string $fund_code) => $fund_code . '.js')), [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 Safari/537.36',
                    'Referer' => 'https://fund.eastmoney.com/',
                ], 6);
                // 处理响应
                $this->_eastmoney_handle($responses['fulfilled']);
            });
        return $retry_list;
    }

    private function _eastmoney_handle(array|Collection $responses)
    {
        if (is_array($responses)) $responses = collect($responses);
        $responses->each(function (\GuzzleHttp\Psr7\Response $response, $fund_code) {
            // $this->info($fund_code);
            $result = $response->getBody()->getContents();
            preg_match('/{[^}]*}/', $result, $matches);
            if (!empty($matches)) {
                $data = Tools::jsonDecode($matches[0]);
                FundValuationUpdateJob::dispatch([
                    'code' => $data['fundcode'],
                    'valuation_time' => $data['gztime'],
                    'valuation_source' => 'https://fundgz.1234567.com.cn/js/{fundCode}.js',
                    'estimated_net_value' => $data['gsz'],
                ]);
            }
        });
    }

    /**
     * 构建GuzzleHttp客户端
     * @param string $base_uri
     * @return Client
     */
    private function build_http_client(string $base_uri, array $headers = [])
    {
        $base_uri = rtrim($base_uri, '/');
        $base_uri_key = urlencode($base_uri);
        if (!isset($this->client[$base_uri_key])) $this->client[$base_uri_key] = new Client([
            'base_uri' => $base_uri . '/',
            'connect_timeout' => 2,
            'timeout' => 5,
            'verify' => false,
            'headers' => $headers,
        ]);
        return $this->client[$base_uri_key];
    }

    /**
     * 单个http get请求
     * @param string $base_uri 接口url
     * @param string $path 接口
     * @param array $params 请求参数
     * @param array $headers 请求头
     * @param int $retryTimes 重试次数
     * @return Response|ResponseInterface
     * @throws GuzzleException
     */
    private function single_http_get(string $base_uri, string $path, array $headers = [], int $retryTimes = 5): Response|ResponseInterface
    {
        $retryTimes = max($retryTimes, 0);
        // $url = Tools::urlRebuild($base_uri . '/' . ltrim($path, '/'), $params);
        // $path = ltrim(str_replace($base_uri, '', $url), '/');
        do {
            try {
                $response = $this->build_http_client($base_uri, $headers)->get($path);
                if ($response->getStatusCode() == 200) return $response;
            } catch (Exception $e) {
                $this->error($e->getMessage());
            }
        } while ($retryTimes >= 0);
        return new Response;
    }

    /**
     * 多个http并发 get请求
     * @param string $base_uri 接口url
     * @param array|Collection $paths 接口
     * @param array $params 请求参数
     * @param array $headers 请求头
     * @param int $retryTimes 重试次数
     * @return array
     */
    private function multi_http_get(string $base_uri, array|\Illuminate\Support\Collection $paths, array $headers = [], int $retryTimes = 5): array
    {
        $successfully = collect();
        $retryTimes = max($retryTimes, 0);
        if (is_array($paths)) $paths = collect($paths);
        if ($paths->isNotEmpty()) {
            do {
                $promises = $paths->map(fn($path) => $this->build_http_client($base_uri, $headers)->getAsync($path));
                $this->info('本次并发请求数量：' . $promises->count());
                // 等待全部请求返回,如果其中一个请求失败会抛出异常
                // $responses = \GuzzleHttp\Promise\Utils::unwrap($promises);
                // 等待全部请求返回,允许某些请求失败
                $responses = Utils::settle($promises->toArray())->wait();
                $responses = collect($responses);
                unset($promises);
                // 成功列表
                $fulfilled = $responses->where('state', 'fulfilled');
                // 失败列表
                $rejected = $responses->where('state', 'rejected');
                unset($responses);
                $this->info('本次并发请求成功响应数量：' . $fulfilled->count());
                $this->info('本次并发请求失败响应数量：' . $rejected->count());
                unset($rejected);
                // 追加成功列表
                $successfully = $successfully->merge($fulfilled->mapWithKeys(fn($item, $key) => [$key => $item['value']]));
                // 匹配成功列表不存在的合集，返回未成功请求的path
                $paths = $paths->diffKeys($fulfilled);
                // 重试次数减一
                $retryTimes--;
            } while ($paths->isNotEmpty() && $retryTimes >= 0);
        }
        return [
            // 成功响应集合
            'fulfilled' => $successfully,
            // 失败请求集合
            'rejected' => $paths,
        ];
    }

}
