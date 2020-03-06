<?php

namespace Bbs\Api;

use Library\Traits\Api;

/**
 * 文章回复 接口服务类
 * Reply
 * @author LYi-Ho 2018-11-24 16:06:44
 */
class Reply extends \Common\Api\Reply
{
    use Api;

    public function getRules()
    {
        $rules = parent::getRules();
        return $rules;
    }

}
