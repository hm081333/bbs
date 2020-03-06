<?php
/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2019-05-09
 * Time: 10:04:04
 */

namespace Common\Model;

use Library\Traits\Model;
use PhalApi\Model\NotORMModel as NotORM;

/**
 * 计划任务 数据层
 * Class Cron
 * @package Common\Model
 * @author  LYi-Ho 2019-05-09 18:51:57
 */
class Cron extends NotORM
{
    use Model;

    protected function getTableName($id)
    {
        return 'cron';
    }

}
