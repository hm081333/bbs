<?php
/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2018-11-26
 * Time: 09:22:44
 */

namespace Common\Model;

use Library\Traits\Model;
use PhalApi\Model\NotORMModel as NotORM;

/**
 * 京东账号 数据层
 * Class JdUser
 * @package Common\Model
 * @author  LYi-Ho 2018-11-26 09:22:44
 */
class JdUser extends NotORM
{
    use Model;

    protected function getTableName($id)
    {
        return 'jd_user';
    }

}
