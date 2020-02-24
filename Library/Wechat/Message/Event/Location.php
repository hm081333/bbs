<?php

namespace Library\Wechat\Message\Event;

use Common\Domain\BaiDuLBS;
use EasyWeChat\Kernel\Contracts\EventHandlerInterface;
use function Common\DI;

/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2017/6/23
 * Time: 下午 10:13
 */
class Location implements EventHandlerInterface
{
    public function handle($payload = null)
    {
        DI()->logger->debug('\Library\Wechat\Message\Event\Location', $payload);

        //file_put_contents(API_ROOT . '/Config/test.php', "<?php   \nreturn " . var_export($inMessage, true) . ';');
        $Location_X = $payload['Location_X'];
        $Location_Y = $payload['Location_Y'];
        // $Scale = $payload['Scale'];
        $rs = BaiDuLBS::location_to_address($Location_X . ',' . $Location_Y);
        return '定位' . $rs['result']['formatted_address'];
    }
}
