<?php
/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2020-03-05
 * Time: 09:01:32
 */

namespace Common\Model;

use Library\Traits\Model;
use PhalApi\Model\NotORMModel as NotORM;

/**
 * 京东签到记录 数据层
 * Class JdSignLog
 * @package Common\Model
 * @author  LYi-Ho 2020-03-05 09:01:32
 */
class JdSignLog extends NotORM
{
    use Model;

    protected function getTableName($id)
    {
        return 'jd_sign_log';
    }

}
