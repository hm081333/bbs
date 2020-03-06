<?php

namespace Admin\Api;

use Library\Traits\Api;

/**
 * 计划任务 接口服务
 * Cron
 * @author  LYi-Ho 2019-05-09 18:03:19
 */
class Cron extends \Common\Api\Cron
{
    use Api;

    public function getRules()
    {
        $rules = parent::getRules();
        return $rules;
    }


}
