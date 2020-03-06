<?php

namespace Admin\Api;

use Library\Traits\Api;

/**
 * 探针模块接口服务
 * Tz
 * @author LYi-Ho 2018-11-24 16:06:44
 */
class Tz extends \Common\Api\Tz
{
    use Api;

    public function getRules()
    {
        $rules = parent::getRules();
        return $rules;
    }


}
