<?php

namespace Sign\Api;

use Library\Traits\Api;

/**
 * 贴吧 接口服务类
 * TieBa
 * @author LYi-Ho 2018-11-24 16:06:44
 */
class TieBa extends \Common\Api\TieBa
{
    use Api;

    public function getRules()
    {
        $rules = parent::getRules();
        return $rules;
    }


}
