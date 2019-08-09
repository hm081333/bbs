<?php

namespace Library\Wechat\Message\Event;

/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2017/6/23
 * Time: 下午 10:13
 */
class Image implements \EasyWeChat\Kernel\Contracts\EventHandlerInterface
{
    public function handle($payload = null)
    {
        \Common\DI()->logger->debug('\Library\Wechat\Message\Event\Image', $payload);
        return '暂不支持上传图片';

        Common_Function::getImage($payload['PicUrl'], $payload['MediaId']);
        // $outMessage = new Wechat_OutMessage_Text();
        // $outMessage->setContent('已收到您的图片');
        return '已收到您的图片';
    }
}