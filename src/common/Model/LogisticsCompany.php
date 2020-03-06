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
 * 物流公司和物流代码 数据层
 * Class LogisticsCompany
 * @package Common\Model
 * @author  LYi-Ho 2018-11-26 09:22:44
 */
class LogisticsCompany extends NotORM
{
    use Model;

    protected function getTableName($id)
    {
        return 'logistics_company';
    }

}
