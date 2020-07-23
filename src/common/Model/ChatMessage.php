<?php

namespace Common\Model;

use Library\Traits\Model;
use PhalApi\Model\NotORMModel as NotORM;


/**
 * 聊天消息 数据层
 * Class ChatMessage
 * @package Common\Model
 * @author  LYi-Ho 2020-05-10 21:45:07
 */
class ChatMessage extends NotORM
{
    use Model;

    protected function getTableName($id)
    {
        return 'chat_message';
    }

}
