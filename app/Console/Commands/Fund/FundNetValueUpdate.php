<?php

namespace App\Console\Commands\Fund;

use App\Jobs\FundNetValueUpdateJob;
use App\Jobs\FundUpdateJob;
use App\Models\Fund\Fund;
use App\Models\Fund\FundNetValue;
use App\Utils\Tools;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Promise\Utils;
use GuzzleHttp\Psr7\Response;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;

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

    private array $client = [];

    private $job_start_time;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->job_start_time = microtime(true);
        $this->comment('获取基金列表');
        $this->_eastmoney();
        $this->info('完成|' . $this->description . '|耗时：' . Tools::secondToTimeText(microtime(true) - $this->job_start_time));
        return Command::SUCCESS;
    }

    private function _eastmoney()
    {
        $request_url = 'https://fund.eastmoney.com/data/rankhandler.aspx?op=ph&dt=kf&ft=all&rs=&gs=0&sc=dm&st=asc&sd=2000-01-01&ed=' . date('Y-m-d') . '&pi={:page}&pn=500&dx=0';
        //region 获取页码与表头
        $retryTimes = 5;
        $max_page = 0;
        while ($max_page <= 0 && $retryTimes > 0) {
            $response = $this->single_http_get('https://fund.eastmoney.com/', str_replace('{:page}', 1, $request_url), [
                'Referer' => 'https://fund.eastmoney.com/data/fundranking.html',
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36',
            ]);
            $content = $response->getBody()->getContents();
            unset($response);
            if (!empty($content)) {
                // region 匹配页码
                preg_match('/allPages:([^,]+),/', $content, $matches);
                if (!empty($matches[1])) $max_page = (int)$matches[1];
                // endregion
                unset($content);
            }
            $retryTimes--;
        }
        //endregion

        $retryTimes = 5;
        $all_pages = collect();
        for ($i = 1; $i <= $max_page; $i++) {
            $all_pages["rankhandler_{$i}"] = str_replace('{:page}', $i, $request_url);
        }
        while ($all_pages->isNotEmpty() && $retryTimes > 0) {
            // 拆分并赋值给新变量，防止后续修改 $all_pages 导致死循环
            $chunks = $all_pages->chunk(10);
            $chunks
                ->each(function (Collection $paths) use (&$all_pages) {
                    $responses = $this->multi_http_get('https://fund.eastmoney.com/', $paths, [
                        'Referer' => 'https://fund.eastmoney.com/data/fundranking.html',
                        'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36',
                    ]);
                    unset($paths);
                    // $this->info('本批并发请求成功数量：' . $responses['fulfilled']->count());
                    // 追加失败重试集合（找出不在本次成功相应集合中的差集，为未成功响应集合）
                    $all_pages = $all_pages->diffKeys($responses['fulfilled']);
                    // dump($all_pages);
                    // 处理响应
                    $responses['fulfilled']->each(function (\GuzzleHttp\Psr7\Response $response, $current_page) {
                        $content = $response->getBody()->getContents();
                        unset($response);
                        if (!empty($content)) {
                            preg_match('/datas:(\[[^]]*])/', $content, $matches);
                            unset($content);
                            $data_json = $matches[1];
                            unset($matches);
                            $data_arr = Tools::jsonDecode($data_json);
                            unset($data_json);
                            foreach ($data_arr as $item) {
                                $item = explode(',', $item);
                                $this->comment("写入基金：{$item[0]}-{$item[1]}");
                                FundUpdateJob::dispatch([
                                    'code' => $item[0],
                                    'name' => $item[1],
                                    'pinyin_initial' => $item[2],
                                    'net_value_time' => $item[3],// 净值更新时间
                                    'unit_net_value' => $item[4],// 单位净值
                                    'cumulative_net_value' => $item[5],// 累计净值
                                ]);
                                if ($item[3]) {
                                    // $this->comment("写入基金净值：{$item[0]}-{$item[1]}，单位净值：{$item[4]}，累计净值：{$item[5]}");
                                    FundNetValueUpdateJob::dispatch([
                                        'code' => $item[0],
                                        'unit_net_value' => $item[4],// 单位净值
                                        'cumulative_net_value' => $item[5],// 累计净值
                                        'net_value_time' => $item[3],// 净值更新时间
                                    ]);
                                }
                            }
                        }
                    });
                    unset($responses);
                });
            $retryTimes--;
            sleep(1);
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
            } catch (\Throwable $e) {
                $this->error($e->getMessage());
            }
            $retryTimes--;
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
                // $this->info('本次并发请求成功响应数量：' . $fulfilled->count());
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
}
