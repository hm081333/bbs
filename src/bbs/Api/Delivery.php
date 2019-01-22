<?php

namespace Bbs\Api;

/**
 * 物流信息 接口服务类
 * Delivery
 * @author LYi-Ho 2018-11-24 16:06:44
 */
class Delivery extends \Common\Api\Delivery
{
    use \Common\Api\Common;

    public function getRules()
    {
        $rules = parent::getRules();
        return $rules;
    }


}
