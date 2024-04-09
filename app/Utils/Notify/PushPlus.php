<?php

namespace App\Utils\Notify;

use App\Contracts\QueueJob;
use App\Exceptions\Request\BadRequestException;
use App\Traits\DefaultQueueJob;
use Illuminate\Support\Facades\Log;


/**
 * Push Plus推送
 */
class PushPlus implements QueueJob
{
    use DefaultQueueJob;

    private string $token;
    private string $title;
    private string $content;
    private string $template;
    private string $topic;
    private string $channel;
    private string $webhook;
    private string $callbackUrl;
    private string $timestamp;
    private string $to;

    /**
     * 快捷返回实例
     *
     * @return static
     */
    public static function instance(): static
    {
        return new static();
    }

    /**
     * 模板（template）枚举。默认使用html模板
     *
     * @return string[]
     */
    public static function templateEnum(): array
    {
        return [
            // 默认模板，支持html文本
            'html',
            // 纯文本展示，不转义html
            'txt',
            // 内容基于json格式展示
            'json',
            // 内容基于markdown格式展示
            'markdown',
            // 阿里云监控报警定制模板
            'cloudMonitor',
            // jenkins插件定制模板
            'jenkins',
            // 路由器插件定制模板
            'route',
            // 支付成功通知模板
            'pay',
        ];
    }

    /**
     * 发送渠道（channel）枚举
     *
     * @return string[]
     */
    public static function channelEnum(): array
    {
        return [
            // 微信公众号
            'wechat',
            // 第三方webhook；HiFlow连接器、企业微信、钉钉、飞书、server酱、IFTTT；webhook机器人推送
            'webhook',
            // 企业微信应用；具体参考企业微信应用推送
            'cp',
            // 邮箱；具体参考邮件渠道使用说明
            'mail',
            // 短信；成功发送1条短信需要10积分（0.1元）
            'sms',
        ];
    }

    //region 设置参数
    public function setToken(string $token): static
    {
        $this->token = trim($token);
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

    public function setTopic(string $topic): static
    {
        $this->topic = trim($topic);
        return $this;
    }

    public function setTemplate(string $template): static
    {
        $template = trim($template);
        if (in_array($template, static::templateEnum())) $this->template = $template;
        return $this;
    }

    public function setChannel(string $channel): static
    {
        $channel = trim($channel);
        if (in_array($channel, static::channelEnum())) $this->channel = $channel;
        return $this;
    }

    public function setWebhook(string $webhook): static
    {
        $this->webhook = trim($webhook);
        return $this;
    }

    public function setCallbackUrl(string $callbackUrl): static
    {
        $this->callbackUrl = trim($callbackUrl);
        return $this;
    }

    public function setTimestamp(string $timestamp): static
    {
        $this->timestamp = trim($timestamp);
        return $this;
    }

    public function setTo(string $to): static
    {
        $this->to = trim($to);
        return $this;
    }

    //endregion

    //region 获取参数
    public function getToken(): ?string
    {
        return $this->token ?? null;
    }

    public function getTitle(): ?string
    {
        return $this->title ?? null;
    }

    public function getContent(): ?string
    {
        return $this->content ?? null;
    }

    public function getTopic(): ?string
    {
        return $this->topic ?? null;
    }

    public function getTemplate(): ?string
    {
        return $this->template ?? 'html';
    }

    public function getChannel(): ?string
    {
        return $this->channel ?? 'wechat';
    }

    public function getWebhook(): ?string
    {
        return $this->webhook ?? null;
    }

    public function getCallbackUrl(): ?string
    {
        return $this->callbackUrl ?? null;
    }

    public function getTimestamp(): ?string
    {
        return $this->timestamp ?? null;
    }

    public function getTo(): ?string
    {
        return $this->to ?? null;
    }
    //endregion


    /**
     * 发送消息推送
     *
     * @return array
     * @throws BadRequestException
     */
    public function handle(): array
    {
        $post_data = array_filter([
            'token' => $this->getToken(),
            'title' => $this->getTitle(),
            'content' => $this->getContent(),
            'topic' => $this->getTopic(),
            'template' => $this->getTemplate(),
            'channel' => $this->getChannel(),
            'webhook' => $this->getWebhook(),
            'callbackUrl' => $this->getCallbackUrl(),
            'timestamp' => $this->getTimestamp(),
            'to' => $this->getTo(),
        ]);
        if (empty($post_data['token']) || empty($post_data['content'])) throw new BadRequestException('参数异常');
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'http://www.pushplus.plus/send',
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_CONNECTTIMEOUT_MS => 60000,
            CURLOPT_TIMEOUT_MS => 60000,
            CURLOPT_SSL_VERIFYPEER => false,
            // CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $post_data,
        ]);
        $response = curl_exec($curl);
        curl_close($curl);
        $result = json_decode($response, true);
        if ($result['code'] != 200) {
            Log::error('Push Plus推送异常|' . $response, $post_data);
        }
        return $result;
    }

}
