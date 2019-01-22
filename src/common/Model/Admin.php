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
 * 管理员 数据层
 * Class Admin
 * @package Common\Model
 * @author  LYi-Ho 2018-11-26 09:22:44
 */
class Admin extends NotORM
{
    use Common;

    protected function getTableName($id)
    {
        return 'admin';
    }

}
