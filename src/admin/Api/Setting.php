<?php

namespace Admin\Api;


use Library\Traits\Api;

/**
 * 管理员模块接口服务类
 * Setting
 * @author LYi-Ho 2018-11-24 16:06:44
 */
class Setting extends \Common\Api\Setting
{
    use Api;

    public function getRules()
    {
        $rules = parent::getRules();
        return $rules;
    }


}
