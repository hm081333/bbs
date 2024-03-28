<?php

namespace App\Utils;

use App\Exceptions\Request\BadRequestException;
use App\Exceptions\Server\InternalServerErrorException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Promise\Utils;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;

class GuzzleHttp
{
    private Client $client;
    private array $user_agent_list = [
        'mac' => [
            'Chrome' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.75 Safari/537.36',
            'Firefox' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.12; rv:65.0) Gecko/20100101 Firefox/65.0',
            'Safari' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.0.3 Safari/605.1.15',
        ],
        'windows' => [
            'Chrome' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 Safari/537.36',
            'Edge' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.140 Safari/537.36 Edge/18.17763',
            'IE' => 'Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; rv:11.0) like Gecko',
        ],
        'ios' => [
            'Chrome' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 7_0_4 like Mac OS X) AppleWebKit/537.51.1 (KHTML, like Gecko) CriOS/31.0.1650.18 Mobile/11B554a Safari/8536.25',
            'Safari' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 8_3 like Mac OS X) AppleWebKit/600.1.4 (KHTML, like Gecko) Version/8.0 Mobile/12F70 Safari/600.1.4',
        ],
        'android' => [
            'Chrome' => 'Mozilla/5.0 (Linux; Android 4.2.1; M040 Build/JOP40D) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.59 Mobile Safari/537.36',
            'Webkit' => 'Mozilla/5.0 (Linux; U; Android 4.4.4; zh-cn; M351 Build/KTU84P) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30',
        ],
    ];

    public function __construct(?string $base_uri = null, ?array $headers = null, ?array $config = null)
    {
        $this->setClient($base_uri, $headers, $config);
    }

    public function setClient(?string $base_uri = null, ?array $headers = null, ?array $config = null): static
    {
        $headers = $headers ?? [];
        $config = $config ?? [];
        if (empty($headers['User-Agent'])) $headers['User-Agent'] = $this->getRandUserAgent();
        if (!empty($base_uri)) $config['base_uri'] = rtrim($base_uri, '/') . '/';
        $config = array_merge([
            'connect_timeout' => 2,
            'timeout' => 5,
            'verify' => false,
            'headers' => $headers,
        ], $config);
        $this->client = new Client($config);
        return $this;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * 获取随机User-Agent
     *
     * @param string|null $device
     *
     * @return string
     * @throws BadRequestException
     */
    private function getRandUserAgent(?string $device = null): string
    {
        if (!$device) $device = array_rand($this->user_agent_list);
        $device_user_agent_list = $this->user_agent_list[$device] ?? [];
        if (empty($device_user_agent_list)) throw new BadRequestException('服务异常');
        return $device_user_agent_list[array_rand($device_user_agent_list)];
    }

    /**
     * 单个http get请求
     *
     * @param string     $uri        接口url
     * @param array|null $headers    请求头
     * @param int        $retryTimes 重试次数
     *
     * @return Response|ResponseInterface
     * @throws InternalServerErrorException
     */
    public function singleRequest(string $method, string $uri = '', ?array $headers = null, int $retryTimes = 5): Response|ResponseInterface
    {
        $options = [];
        if ($headers) $options['headers'] = $headers;
        $retryTimes = max($retryTimes, 0);
        do {
            try {
                $response = $this->getClient()->request($method, $uri, $options);
                // $response = $this->getClient()->get($uri, $options);
                if ($response->getStatusCode() == 200) return $response;
            } catch (\Throwable $e) {
                throw new InternalServerErrorException($e->getMessage());
            }
            $retryTimes--;
        } while ($retryTimes >= 0);
        return new Response;
    }

    /**
     * 多个http并发 get请求
     *
     * @param array|Collection $paths      接口
     * @param array|null       $headers    请求头
     * @param int              $retryTimes 重试次数
     *
     * @return array
     */
    public function multiRequest(string $method, array|Collection $paths = [], ?array $headers = null, int $retryTimes = 5): array
    {
        $options = [];
        if ($headers) $options['headers'] = $headers;
        $successfully = collect();
        $retryTimes = max($retryTimes, 0);
        if (is_array($paths)) $paths = collect($paths);
        if ($paths->isNotEmpty()) {
            do {
                $promises = $paths->map(fn($path) => $this->getClient()->requestAsync($method, $path, $options));
                // $this->info('本次并发请求数量：' . $promises->count());
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
                // $this->info('本次并发请求失败响应数量：' . $rejected->count());
                // $rejected->each(fn($value) => $this->error($value['reason']->getMessage()));
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
