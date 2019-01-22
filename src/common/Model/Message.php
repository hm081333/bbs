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
 * 短信、邮件模板 数据层
 * Class Message
 * @package Common\Model
 * @author  LYi-Ho 2018-11-26 09:22:44
 */
class Message extends NotORM
{
    use Common;

    protected function getTableName($id)
    {
        return 'message';
    }

}
