<?php
/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2018-11-26
 * Time: 09:22:44
 */

namespace Common\Model;

use PhalApi\Model\NotORMModel as NotORM;

/**
 * 京东签到项 数据层
 * Class JdSignItem
 * @package Common\Model
 * @author  LYi-Ho 2018-11-26 09:22:44
 */
class JdSignItem extends NotORM
{
    use Common;

    protected function getTableName($id)
    {
        return 'jd_sign_item';
    }

}
