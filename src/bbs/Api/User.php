<?php

namespace Bbs\Api;

/**
 * 用户模块接口服务
 * User
 * @author LYi-Ho 2018-11-24 16:06:44
 */
class User extends \Common\Api\User
{
    public function getRules()
    {
        $rules = parent::getRules();
        return $rules;
    }

}
