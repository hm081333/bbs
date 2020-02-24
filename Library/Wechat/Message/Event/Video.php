<?php

namespace Library\Wechat\Message\Event;

use EasyWeChat\Kernel\Contracts\EventHandlerInterface;
use function Common\DI;

/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2017/6/23
 * Time: 下午 10:13
 */
class Video implements EventHandlerInterface
{
    public function handle($payload = null)
    {
        DI()->logger->debug('\Library\Wechat\Message\Event\Video', $payload);
        return '欢迎关注';
    }
}
