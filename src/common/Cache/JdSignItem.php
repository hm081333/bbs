<?php
/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2020-03-04
 * Time: 17:39:15
 */

namespace Common\Cache;

use function Common\DI;

/**
 * 京东签到项 缓存层
 * Class JdSignItem
 * @package Common\Cache
 * @author  LYi-Ho 2018-11-26 09:22:44
 */
class JdSignItem extends Common
{
    protected function getTableName()
    {
        return 'jd_sign_item';
    }

}
