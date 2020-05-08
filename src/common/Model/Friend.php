<?php

namespace Common\Model;

use Library\Traits\Model;
use PhalApi\Model\NotORMModel as NotORM;


/**
 * 好友 数据层
 * Class Friend
 * @package Common\Model
 * @author  LYi-Ho 2020-05-08 10:45:07
 */
class Friend extends NotORM
{
    use Model;

    protected function getTableName($id)
    {
        return 'friend';
    }

}
