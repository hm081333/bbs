<?php
/**
 * 统一访问入口
 */
// 引入我们的主项目工厂类。

require_once dirname(__FILE__) . '/init.php';

$app = \Common\DI()->wechat;

$app->server->push(\Library\Wechat\Message\Event\Text::class, \EasyWeChat\Kernel\Messages\Message::TEXT);
$app->server->push(\Library\Wechat\Message\Event\Image::class, \EasyWeChat\Kernel\Messages\Message::IMAGE);
$app->server->push(\Library\Wechat\Message\Event\Voice::class, \EasyWeChat\Kernel\Messages\Message::VOICE);
$app->server->push(\Library\Wechat\Message\Event\Video::class, \EasyWeChat\Kernel\Messages\Message::VIDEO);
$app->server->push(\Library\Wechat\Message\Event\ShortVideo::class, \EasyWeChat\Kernel\Messages\Message::SHORT_VIDEO);
$app->server->push(\Library\Wechat\Message\Event\Location::class, \EasyWeChat\Kernel\Messages\Message::LOCATION);
$app->server->push(\Library\Wechat\Message\Event\DeviceEvent::class, \EasyWeChat\Kernel\Messages\Message::DEVICE_EVENT);
$app->server->push(\Library\Wechat\Message\Event\DeviceText::class, \EasyWeChat\Kernel\Messages\Message::DEVICE_TEXT);
$app->server->push(\Library\Wechat\Message\Event\File::class, \EasyWeChat\Kernel\Messages\Message::FILE);
$app->server->push(\Library\Wechat\Message\Event\TextCard::class, \EasyWeChat\Kernel\Messages\Message::TEXT_CARD);
$app->server->push(\Library\Wechat\Message\Event\Transfer::class, \EasyWeChat\Kernel\Messages\Message::TRANSFER);
$app->server->push(\Library\Wechat\Message\Event\Event::class, \EasyWeChat\Kernel\Messages\Message::EVENT);
$app->server->push(\Library\Wechat\Message\Event\MiniProgramPage::class, \EasyWeChat\Kernel\Messages\Message::MINIPROGRAM_PAGE);

$response = $app->server->serve();

// 将响应输出
$response->send();
exit; // Laravel 里请使用：return $response;

