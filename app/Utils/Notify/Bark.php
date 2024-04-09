<?php

namespace App\Utils\Notify;

use App\Contracts\QueueJob;
use App\Exceptions\Request\BadRequestException;
use App\Traits\DefaultQueueJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * static推送
 */
class Bark implements QueueJob
{
    use DefaultQueueJob;

    private string $server_address;
    private string $device_key;
    private string $base_group;
    private string $title;
    private string $body;
    private string $level;
    private int $badge;
    private string $autoCopy;
    private string $copy;
    private string $sound;
    private string $icon;
    private string $group;
    private int $isArchive;
    private string $url;

    /**
     * 快捷返回实例
     *
     * @return static
     */
    public static function instance(): static
    {
        return new static();
    }

    public static function levelEnum(): array
    {
        return [
            'active',
            'timeSensitive',
            'passive',
        ];
    }

    //region 设置参数

    /**
     * 服务器地址
     *
     * @param string $server_address
     *
     * @return static
     * @throws BadRequestException
     */
    public function setServerAddress(string $server_address): static
    {
        if (!Str::isUrl($server_address)) throw new BadRequestException('参数异常');
        $this->server_address = rtrim(trim($server_address), '/');
        return $this;
    }

    /**
     * 设备Key
     *
     * @param string $device_key
     *
     * @return static
     */
    public function setDeviceKey(string $device_key): static
    {
        $this->device_key = trim($device_key);
        return $this;
    }

    /**
     * 推送消息基础分组
     *
     * @param string $group
     *
     * @return static
     */
    public function setBaseGroup(string $group): static
    {
        $this->base_group = trim($group);
        return $this;
    }

    /**
     * 推送标题
     *
     * @param string $title
     *
     * @return static
     */
    public function setTitle(string $title): static
    {
        $this->title = trim($title);
        return $this;
    }

    /**
     * 推送内容
     *
     * @param string $body
     *
     * @return static
     */
    public function setBody(string $body): static
    {
        $this->body = trim($body);
        return $this;
    }

    /**
     * 推送中断级别。
     * active：默认值，系统会立即亮屏显示通知
     * timeSensitive：时效性通知，可在专注状态下显示通知。
     * passive：仅将通知添加到通知列表，不会亮屏提醒。
     *
     * @param string $level
     *
     * @return static
     */
    public function setLevel(string $level): static
    {
        $level = trim($level);
        if (in_array($level, static::levelEnum())) $this->level = $level;
        return $this;
    }

    /**
     * 设置角标
     *
     * @param int $badge
     *
     * @return static
     */
    public function setBadge(int $badge): static
    {
        $this->badge = trim($badge);
        return $this;
    }

    /**
     * iOS14.5以下自动复制推送内容，iOS14.5以上需手动长按推送或下拉推送
     *
     * @param string $autoCopy
     *
     * @return static
     */
    public function setAutoCopy(string $autoCopy): static
    {
        $this->autoCopy = trim($autoCopy);
        return $this;
    }

    /**
     * 复制推送时，指定复制的内容，不传此参数将复制整个推送内容。
     *
     * @param string $copy
     *
     * @return static
     */
    public function setCopy(string $copy): static
    {
        $this->copy = trim($copy);
        return $this;
    }

    /**
     * 可以为推送设置不同的铃声
     *
     * @param string $sound
     *
     * @return static
     */
    public function setSound(string $sound): static
    {
        $this->sound = trim($sound);
        return $this;
    }

    /**
     * 为推送设置自定义图标，设置的图标将替换默认static图标。
     * 图标会自动缓存在本机，相同的图标 URL 仅下载一次。
     *
     * @param string $icon
     *
     * @return static
     */
    public function setIcon(string $icon): static
    {
        $this->icon = trim($icon);
        return $this;
    }

    /**
     * 推送消息分组
     *
     * @param string $group
     *
     * @return static
     */
    public function setGroup(string $group): static
    {
        $this->group = trim($group);
        return $this;
    }

    /**
     * 传 1 保存推送，传其他的不保存推送，不传按APP内设置来决定是否保存。
     *
     * @param int|string $isArchive
     *
     * @return static
     */
    public function setIsArchive(int|string $isArchive): static
    {
        $this->isArchive = $isArchive == 1 ? 1 : trim($isArchive);
        return $this;
    }

    /**
     * 点击推送时，跳转的URL ，支持URL Scheme 和 Universal Link
     *
     * @param string $url
     *
     * @return static
     */
    public function setUrl(string $url): static
    {
        if (!Str::isUrl($url)) throw new BadRequestException('参数异常');
        $this->url = trim($url);
        return $this;
    }
    //endregion

    //region 获取参数

    /**
     * 服务器地址
     *
     * @return string
     */
    public function getServerAddress(): string
    {
        return $this->server_address ?? 'https://api.day.app';
    }

    /**
     * 设备Key
     *
     * @return string
     */
    public function getDeviceKey(): string
    {
        return $this->device_key ?? 'EwFyrH2XDfuZx2McLMJT8b';
    }

    /**
     * 推送标题
     *
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title ?? null;
    }

    /**
     * 推送内容
     *
     * @return string|null
     */
    public function getBody(): ?string
    {
        return $this->body ?? null;
    }

    /**
     * 推送中断级别。
     * active：默认值，系统会立即亮屏显示通知
     * timeSensitive：时效性通知，可在专注状态下显示通知。
     * passive：仅将通知添加到通知列表，不会亮屏提醒。
     *
     * @return string
     */
    public function getLevel(): string
    {
        return $this->level ?? 'active';
    }

    /**
     * 设置角标
     *
     * @return int|null
     */
    public function getBadge(): ?int
    {
        return $this->badge ?? null;
    }

    /**
     * iOS14.5以下自动复制推送内容，iOS14.5以上需手动长按推送或下拉推送
     *
     * @return string|null
     */
    public function getAutoCopy(): ?string
    {
        return $this->autoCopy ?? null;
    }

    /**
     * 复制推送时，指定复制的内容，不传此参数将复制整个推送内容。
     *
     * @return string|null
     */
    public function getCopy(): ?string
    {
        return $this->copy ?? null;
    }

    /**
     * 可以为推送设置不同的铃声
     *
     * @return string|null
     */
    public function getSound(): ?string
    {
        return $this->sound ?? null;
    }

    /**
     * 为推送设置自定义图标，设置的图标将替换默认string图标。
     * 图标会自动缓存在本机，相同的图标 URL 仅下载一次。
     *
     * @return string|null
     */
    public function getIcon(): ?string
    {
        return $this->icon ?? null;
    }

    /**
     * 推送消息分组
     *
     * @return string
     */
    public function getGroup(): string
    {
        return ($this->base_group ?? config('app.name')) . (isset($this->group) ? ":{$this->group}" : '');
    }

    /**
     * 传 1 保存推送，传其他的不保存推送，不传按APP内设置来决定是否保存。
     *
     * @return int|null
     */
    public function getIsArchive(): ?int
    {
        return $this->url ?? null;
    }

    /**
     * 点击推送时，跳转的URL ，支持URL Scheme 和 Universal Link
     *
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url ?? null;
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
        $device_key = $this->getDeviceKey();
        if (empty($device_key)) throw new BadRequestException('参数异常');
        $post_data = array_filter([
            'title' => $this->getTitle(),
            'body' => $this->getBody(),
            'level' => $this->getLevel(),
            'badge' => $this->getBadge(),
            'autoCopy' => $this->getAutoCopy(),
            'copy' => $this->getCopy(),
            'sound' => $this->getSound(),
            'icon' => $this->getIcon(),
            'group' => $this->getGroup(),
            'isArchive' => $this->getIsArchive(),
            'url' => $this->getUrl(),
        ]);
        $url = $this->getServerAddress() . '/' . $device_key;
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
            Log::error('static推送异常|' . $url . '|' . $response, $post_data);
        }
        return $result;
    }

}
