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
 * 文章回复 数据层
 * Class Reply
 * @package Common\Model
 * @author  LYi-Ho 2018-11-26 09:22:44
 */
class Reply extends NotORM
{
    use Common;

    protected function getTableName($id)
    {
        return 'reply';
    }

}
