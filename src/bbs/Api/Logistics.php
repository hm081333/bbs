<?php

namespace Bbs\Api;

use Library\Traits\Api;

/**
 * 物流公司 接口服务类
 * Logistics
 * @author LYi-Ho 2018-11-24 16:06:44
 */
class Logistics extends \Common\Api\Logistics
{
    use Api;

    public function getRules()
    {
        $rules = parent::getRules();
        return $rules;
    }


}
