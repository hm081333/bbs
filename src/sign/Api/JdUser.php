<?php

namespace Sign\Api;

use Library\Traits\Api;

/**
 * 京东账号 接口服务类
 * JdUser
 * @author LYi-Ho 2018-11-24 16:06:44
 */
class JdUser extends \Common\Api\JdUser
{
    use Api;

    public function getRules()
    {
        $rules = parent::getRules();
        return $rules;
    }

}
