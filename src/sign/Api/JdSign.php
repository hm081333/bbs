<?php

namespace Sign\Api;

/**
 * 京东签到 接口服务类
 * JdSign
 * @author LYi-Ho 2018-11-24 16:06:44
 */
class JdSign extends \Common\Api\JdSign
{
    use \Common\Api\Common;

    public function getRules()
    {
        $rules = parent::getRules();
        return $rules;
    }

}
