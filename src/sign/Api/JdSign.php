<?php

namespace Sign\Api;

use Library\Traits\Api;

/**
 * 京东签到 接口服务类
 * JdSign
 * @author LYi-Ho 2018-11-24 16:06:44
 */
class JdSign extends \Common\Api\JdSign
{
    use Api;

    public function getRules()
    {
        $rules = parent::getRules();
        return $rules;
    }

}
