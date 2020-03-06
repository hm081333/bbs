<?php
/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2019-05-09
 * Time: 18:03:19
 */

namespace Common\Api;


use Library\Traits\Api;

/**
 * 计划任务 接口服务
 * Cron
 * @author  LYi-Ho 2019-05-09 18:03:19
 */
class Cron extends Base
{
    use Api;

    public function getRules()
    {
        $rules = parent::getRules();
        return $rules;
    }

    /**
     * 计划任务 领域层
     * @return \Common\Domain\Cron
     */
    private function cronDomain()
    {
        return self::getDomain();
    }

}
