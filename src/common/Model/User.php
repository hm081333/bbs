<?php
/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2018/11/24
 * Time: 17:35
 */

namespace Common\Model;

use PhalApi\Model\NotORMModel as NotORM;


/**
 * 用户 数据层
 * Class User
 * @package Common\Model
 * @author  LYi-Ho 2018-11-26 09:24:07
 */
class User extends NotORM
{
    use Common;

    protected function getTableName($id)
    {
        return 'user';
    }

}
