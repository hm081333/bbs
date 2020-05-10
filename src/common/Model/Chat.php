<?php

namespace Common\Model;

use Library\Traits\Model;
use PhalApi\Model\NotORMModel as NotORM;


/**
 * 聊天 数据层
 * Class Chat
 * @package Common\Model
 * @author  LYi-Ho 2020-05-10 21:45:07
 */
class Chat extends NotORM
{
    use Model;

    protected function getTableName($id)
    {
        return 'chat';
    }

}
