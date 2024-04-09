<?php

namespace App\Utils\Notify;

use App\Contracts\QueueJob;
use App\Exceptions\Request\BadRequestException;
use App\Traits\DefaultQueueJob;
use Illuminate\Support\Facades\Log;


/**
 * 钉钉机器人推送
 */
class DingDingBot implements QueueJob
{
    use DefaultQueueJob;

    private string $token;
    private string $secret;
    private string $title;
    private string $content;

    /**
     * 快捷返回实例
     *
     * @return static
     */
    public static function instance(): static
    {
        return new static();
    }

    //region 设置参数
    public function setToken(string $token): static
    {
        $this->token = trim($token);
        return $this;
    }

    public function setSecret(string $secret): static
    {
        $this->secret = trim($secret);
        return $this;
    }

    public function setTitle(string $title): static
    {
        $this->title = trim($title);
        return $this;
    }

    public function setContent(string $content): static
    {
        $this->content = trim($content);
        return $this;
    }
    //endregion

    //region 获取参数
    public function getToken(): ?string
    {
        return $this->token ?? null;
    }

    public function getSecret(): ?string
    {
        return $this->secret ?? null;
    }

    public function getTitle(): ?string
    {
        return $this->title ?? null;
    }

    public function getContent(): ?string
    {
        return $this->content ?? null;
    }

    //endregion

    /**
     * 构建URL请求参数
     *
     * @return string
     * @throws BadRequestException
     */
    private function buildParams(): string
    {
        $params = [
            'access_token' => $this->getToken(),
        ];
        if (empty($params['access_token'])) throw new BadRequestException('参数异常');
        // 加签秘钥
        $secret = $this->getSecret();
        // 有设置加签秘钥，追加签名
        if ($secret) {
            // 将时间戳 timestamp 和密钥 secret 当做签名字符串，使用HmacSHA256算法计算签名，然后进行Base64 encode，最后再把签名参数再进行urlEncode，得到最终的签名（需要使用UTF-8字符集）。
            $params['timestamp'] = (string)round(microtime(true) * 1000);
            $string_to_sign = $params['timestamp'] . "\n" . $secret;
            $hmac_code = hash_hmac('sha256', $string_to_sign, $secret, true);
            $params['sign'] = urlencode(base64_encode($hmac_code));
        }
        return http_build_query($params);
    }

    /**
     * 发送消息推送
     *
     * @return array
     * @throws BadRequestException
     */
    public function handle(): array
    {
        $title = $this->getTitle();
        $content = $this->getContent();
        if (empty($content)) throw new BadRequestException('参数异常');
        $post_data = [
            'msgtype' => 'text',
            'text' => [
                'content' => $title ? "{$title}\n\n{$content}" : $content,
            ],
        ];
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://oapi.dingtalk.com/robot/send' . '?' . $this->buildParams(),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => json_encode($post_data),
            CURLOPT_HEADER => 0,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CONNECTTIMEOUT_MS => 60000,
            CURLOPT_TIMEOUT_MS => 60000,
            // CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
        ]);
        $response = curl_exec($curl);
        curl_close($curl);
        $result = json_decode($response, true);
        if ($result['errcode'] != 200) {
            Log::error('钉钉机器人推送异常|' . $response, $post_data);
        }
        return $result;
    }

}
