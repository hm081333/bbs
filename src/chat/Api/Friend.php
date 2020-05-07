<?php

namespace Chat\Api;

use Library\Traits\Api;

/**
 * 好友模块接口服务
 * Friend
 * @author LYi-Ho 2020-05-08 12:08:17
 */
class Friend extends \Common\Api\Friend
{
    use Api;

    public function getRules()
    {
        $rules = parent::getRules();
        return $rules;
    }

    public function list()
    {
        $data = \Common\DI()->request->getAll();
        var_dump($data);
        return $data;
    }

}
