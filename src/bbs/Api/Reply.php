<?php

namespace Bbs\Api;

/**
 * 文章回复 接口服务类
 * Reply
 * @author LYi-Ho 2018-11-24 16:06:44
 */
class Reply extends \Common\Api\Reply
{
    public function getRules()
    {
        $rules = parent::getRules();
        return $rules;
    }

}
