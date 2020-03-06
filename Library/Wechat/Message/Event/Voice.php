<?php

namespace Library\Wechat\Message\Event;

use Common\Domain\TuLing;
use EasyWeChat\Kernel\Contracts\EventHandlerInterface;
use EasyWeChat\Kernel\Messages\News as ReturnNews;
use EasyWeChat\Kernel\Messages\NewsItem as ReturnNewsItem;
use function Common\DI;

/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2017/6/23
 * Time: 下午 10:13
 */
class Voice implements EventHandlerInterface
{
    public function handle($payload = null)
    {
        DI()->logger->debug('\Library\Wechat\Message\Event\Voice', $payload);
        // return;

        $ask = TuLing::get($payload['recognition']);
        if ($ask['code'] == '100000') {
            return $ask['text'];
        } else if (($ask['code'] == '200000' || $ask['code'] == '302000') && empty($ask['list'])) {
            $items = [
                new ReturnNewsItem([
                    'title' => $payload['Content'],
                    'description' => $ask['text'],
                    'url' => $ask['url'],
                    // 'image'       => $ask['url'],
                ]),
            ];
            return new ReturnNews($items);
        } else {
            // file_put_contents(API_ROOT . '/Config/test.php', "<?php   \nreturn " . var_export($ask, true) . ';');
            $items = [];
            foreach ($ask['list'] as $key => $rs) {
                if ($key >= 8) {
                    break;
                }
                $items[] = new ReturnNewsItem([
                    'title' => $rs['name'],
                    'description' => $rs['info'],
                    'url' => $rs['detailurl'],
                    'image' => $rs['icon'],
                ]);
            }
            return new ReturnNews($items);
        }
    }
}
