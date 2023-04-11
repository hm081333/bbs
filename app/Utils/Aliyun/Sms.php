<?php

namespace App\Utils\Aliyun;

use AlibabaCloud\SDK\Dysmsapi\V20170525\Dysmsapi;
use AlibabaCloud\SDK\Dysmsapi\V20170525\Models\SendSmsRequest;
use Darabonba\OpenApi\Models\Config;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use function config;

class Sms
{
    /**
     * @var Repository|Application|mixed
     */
    private $config;
    /**
     * @var Dysmsapi
     */
    private $client;

    public function __construct()
    {
        $this->config = config('sms.configs.aliyun');
        $config = new Config([
            // 您的AccessKey ID
            "accessKeyId" => $this->config['access_key_id'],
            // 您的AccessKey Secret
            "accessKeySecret" => $this->config['access_key_secret'],
        ]);
        // 访问的域名
        $config->endpoint = "dysmsapi.aliyuncs.com";
        $this->client = new Dysmsapi($config);
    }

    public function sendSms($mobile, $code, $params = [])
    {
        $sendSmsRequest = new SendSmsRequest([
            "phoneNumbers" => $mobile,
            "signName" => $this->config['sign_name'],
            "templateCode" => $code,
            "templateParam" => json_encode($params, true),
        ]);
        // 复制代码运行请自行打印 API 的返回值
        $res = $this->client->sendSms($sendSmsRequest);
        return $res->toMap();
    }

}
