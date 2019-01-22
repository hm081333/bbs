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
 * 科目 数据层
 * Class Subject
 * @package Common\Model
 * @author  LYi-Ho 2018-11-26 09:23:27
 */
class Subject extends NotORM
{
    use Common;

    protected function getTableName($id)
    {
        return 'class';
    }

}
