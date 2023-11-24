<?php

namespace App\Console\Commands\Fund;

use App\Exceptions\Server\InternalServerErrorException;
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
use Psr\SimpleCache\InvalidArgumentException;

class FundValuationUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fund:valuation-update
    {--eastmoney : 同步（天天基金网）估值}
    {--test : 测试}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '基金：估值更新';
    private array $client = [];
    private $job_start_time;

    /**
     * Execute the console command.
     *
     * @return int
     * @throws InternalServerErrorException
     * @throws InvalidArgumentException
     */
    public function handle()
    {
        $this->job_start_time = microtime(true);
        $this->comment('获取基金估值');
        $now_time = Tools::now();
        if (
            $now_time->lt(date('Y-m-d 9:25'))
            ||
            (
                $now_time->gt(date('Y-m-d 11:40'))
                &&
                $now_time->lt(date('Y-m-d 12:55'))
            )
            ||
            $now_time->gt(date('Y-m-d 15:10'))
            ||
            $now_time->isWeekend()
            ||
            Calendar::isHoliday($now_time)
        ) {
            $this->comment('不在基金开门时间');
            if (!$this->option('test')) return Command::SUCCESS;
        }
        if ($this->option('eastmoney')) {
            // 天天基金网
            $this->_eastmoney();
        } else {
            // 基金速查网
            $this->_dayfund();
        }
        $this->info('完成|' . $this->description . '|耗时：' . Tools::secondToTimeText(microtime(true) - $this->job_start_time));
        // 调起写入命令行
        $this->call('fund:valuation-write');
        return Command::SUCCESS;
    }

    /**
     * 基金速查网
     * @return void
     * @throws GuzzleException
     */
    private function _dayfund()
    {
        $valuation_source = 'https://www.dayfund.cn/prevalue.html';
        $table_headers = [];
        //region 获取页码与表头
        $retryTimes = 5;
        $max_page = 0;
        while ($max_page <= 0 && $retryTimes > 0) {
            $response = $this->single_http_get('https://www.dayfund.cn/', "prevalue_10000.html", [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 Safari/537.36',
            ]);
            // 获取html中body
            preg_match('/<body>.*?<\/body>/', str_replace(["\r", "\n", "\r\n", "<br/>"], '', $response->getBody()->getContents()), $matches);
            if (!empty($body = $matches[0] ?? '')) {
                // region 匹配页码
                preg_match_all('/prevalue_(\d+).html/', $body, $matches);
                if (!empty($matches[1])) $max_page = (int)max($matches[1]) + 1;
                // endregion
                // region 匹配表格内容
                preg_match('/<table>.*?<\/table>/', $body, $matches);
                if (!empty($table = $matches[0])) {
                    preg_match_all('/<tr[^>]*>(.*?)<\/tr>/', $table, $matches);
                    if (!empty($matches[1]) && !empty($tr = $matches[1][0])) {
                        preg_match_all('/<td[^>]*>(.*?)<\/td>/', $tr, $matches);
                        $table_headers = array_map(function ($value) {
                            return strip_tags($value);
                        }, $matches[1]);
                    }
                }
                //endregion
            }
            $retryTimes--;
        }
        //endregion

        $retryTimes = 5;
        $all_pages = collect();
        for ($i = 1; $i <= $max_page; $i++) {
            $all_pages["prevalue_{$i}"] = "prevalue_{$i}.html";
        }
        while ($all_pages->isNotEmpty() && $retryTimes > 0) {
            $chunks = $all_pages->chunk(10);
            $chunks
                ->each(function (Collection $paths) use (&$all_pages, $valuation_source, $table_headers) {
                    $responses = $this->multi_http_get('https://www.dayfund.cn/', $paths, [
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 Safari/537.36',
                    ]);
                    $this->info('本批并发请求成功数量：' . $responses['fulfilled']->count());
                    // 追加失败重试集合
                    $all_pages = $all_pages->diffKeys($responses['fulfilled']);
                    // dump($all_pages);
                    // 处理响应
                    $responses['fulfilled']->each(function (\GuzzleHttp\Psr7\Response $response, $current_page) use ($valuation_source, $table_headers) {
                        $page_content = str_replace(["\r", "\n", "\r\n", "<br/>"], '', $response->getBody()->getContents());
                        // 获取html中body
                        preg_match('/<body>.*?<\/body>/', $page_content, $matches);
                        if (!empty($body = $matches[0] ?? '')) {
                            // region 匹配表格内容
                            preg_match('/<table>.*?<\/table>/', $body, $matches);
                            if (empty($matches[0])) {
                                Log::debug($current_page);
                                Log::debug($page_content);
                                return;
                            }
                            $table = $matches[0];
                            preg_match_all('/<tr[^>]*>(.*?)<\/tr>/', $table, $matches);
                            if (empty($matches[1])) {
                                Log::debug($current_page);
                                Log::debug($page_content);
                                return;
                            }
                            foreach ($matches[1] as $index => $tr) {
                                preg_match_all('/<td[^>]*>(.*?)<\/td>/', $tr, $matches);
                                $values = array_map(function ($value) {
                                    return strip_tags($value);
                                }, $matches[1]);
                                if ($index == 0) continue;
                                if (count($values) == count($table_headers)) {
                                    $funds_value = array_combine($table_headers, $values);
                                    //$this->comment("写入基金估值：{$funds_value['估值时间']}-{$funds_value['基金代码']}-{$funds_value['基金名称']}-{$funds_value['最新预估净值']}");
                                    // 只写入当天的估值
                                    $this->dispatchFundValuationUpdateJob([
                                        'code' => $funds_value['基金代码'],
                                        'valuation_time' => $funds_value['估值时间'],
                                        'valuation_source' => $valuation_source,
                                        'estimated_net_value' => $funds_value['最新预估净值'],
                                    ]);
                                }
                            }
                            // endregion
                        }
                    });
                });
            $retryTimes--;
            sleep(1);
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
                $this->info('本批并发请求成功数量：' . $responses['fulfilled']->count());
                // 追加失败重试集合
                $retry_list = $retry_list->merge($responses['rejected']->keys());
                // 处理响应
                $responses['fulfilled']->each(fn(\GuzzleHttp\Psr7\Response $response, $fund_code) => $this->_eastmoney_handle($response->getBody()->getContents()));
            });
        return $retry_list;
    }

    private function _eastmoney_handle(string $content)
    {
        preg_match('/{[^}]*}/', $content, $matches);
        if (!empty($matches)) {
            $data = Tools::jsonDecode($matches[0]);
            $this->dispatchFundValuationUpdateJob([
                'code' => $data['fundcode'],
                'valuation_time' => $data['gztime'],
                'valuation_source' => 'https://fundgz.1234567.com.cn/js/{fundCode}.js',
                'estimated_net_value' => $data['gsz'],
            ]);
        }
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
                $rejected->each(function ($value) {
                    $this->error($value['reason']->getMessage());
                });
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

    /**
     * 调度队列任务
     * @param array $data
     * @return false|\Illuminate\Foundation\Bus\PendingDispatch
     */
    private function dispatchFundValuationUpdateJob(array $data): \Illuminate\Foundation\Bus\PendingDispatch|bool
    {
        if (!Carbon::parse($data['valuation_time'])->isToday()) return false;
        return FundValuationUpdateJob::dispatchSync($data);
    }

}
