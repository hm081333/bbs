<?php

namespace App\Utils;

use Illuminate\Support\Facades\Log;

/**
 * Bark推送
 */
class Bark
{
    private $device_key;
    private $group;
    private $sound;
    private $title;
    private $body = '';
    private $badge;
    private $icon;
    private $url;

    /**
     * 设备Token
     * @param string $device_key
     * @return Bark
     */
    public function setDeviceKey(string $device_key): Bark
    {
        $this->device_key = $device_key;
        return $this;
    }

    /**
     * 推送消息分组
     * @param string $group
     * @return Bark
     */
    public function setGroup(string $group): Bark
    {
        $this->group = $this->group . ($group ? ":{$group}" : '');
        return $this;
    }

    /**
     * 推送铃声
     * @param string|null $sound
     * @return Bark
     */
    public function setSound(string $sound): Bark
    {
        $this->sound = $sound;
        return $this;
    }

    /**
     * 推送标题
     * @param string $title
     * @return Bark
     */
    public function setTitle(string $title): Bark
    {
        $this->title = $title;
        return $this;
    }

    /**
     * 推送内容
     * @param string $body
     * @return Bark
     */
    public function setBody(string $body): Bark
    {
        $this->body = $body;
        return $this;
    }

    /**
     * 设置角标
     * @param int $badge
     * @return Bark
     */
    public function setBadge(int $badge): Bark
    {
        $this->badge = $badge;
        return $this;
    }

    /**
     * 推送图标
     * @param string $icon
     * @return Bark
     */
    public function setIcon(string $icon): Bark
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * 携带url
     * @param string $url
     * @return Bark
     */
    public function setUrl(string $url): Bark
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @param string $device_key 设备Token
     * @param string $group      消息分组
     * @param string $sound      推送铃声
     */
    public function __construct($device_key = null, $group = null, $sound = null)
    {
        $this->setDeviceKey($device_key ?: 'EwFyrH2XDfuZx2McLMJT8b');
        $this->group = config('app.name');
        if ($group) $this->setGroup($group);
        if ($sound) $this->setSound($sound);
    }

    /**
     * 快捷返回实例
     * @param $device_key
     * @param $group
     * @param $sound
     * @return static
     */
    public static function instance($device_key = null, $group = null, $sound = null)
    {
        return new static($device_key, $group, $sound);
    }

    /**
     * 发送消息推送
     * @param string $content
     * @return void
     */
    public function send(string $content = '')
    {
        if (empty($this->body)) $this->setBody($content);
        $url = 'https://api.day.app/push';
        $post_data = [
            "body" => $this->body,
            "device_key" => $this->device_key,
            "title" => $this->title,
            "badge" => $this->badge,
            "sound" => $this->sound,
            "icon" => $this->icon,
            "group" => $this->group,
            "url" => $this->url,
        ];
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
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
            Log::error('Bark推送异常|' . $response);
        }
        // var_dump($result);
        return $result;
    }

}
