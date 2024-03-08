<?php

namespace App\Utils\Juhe;

use App\Exceptions\Server\InternalServerErrorException;
use App\Utils\CUrl;
use App\Utils\Tools;
use Exception;

class Utils
{
    /**
     * 构造函数
     * @throws Exception
     */
    public function __construct()
    {
        $this->curl = new Curl(5);
        $this->key = config('third.juhe.key');
    }

    /**
     * 创建实例
     * @return static
     */
    public static function instance(): static
    {
        return new static();
    }

    /**
     * 请求
     * @param string $url 接口地址
     * @param array $params 请求参数
     * @param array $data 请求数据
     * @return array
     * @throws InternalServerErrorException
     * @throws Exception
     */
    public function request(string $url, array $params = [], array $data = []): array
    {
        $params['key'] = $this->key;
        if (!empty($params)) $url = Tools::urlRebuild($url, $params);
        $response_string = $this->curl->setHeader([
            'Content-Type' => 'application/x-www-form-urlencoded',
        ])->post($url, $data);
        $response_data = json_decode($response_string, true);
        if ($response_data['error_code'] != 0) throw new InternalServerErrorException($response_data['reason']);
        return $response_data['result'];
    }
}
