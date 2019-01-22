<?php

namespace Bbs\Api;


/**
 * 文章接口服务类
 * Topic
 * @author LYi-Ho 2018-11-24 16:06:44
 */
class Topic extends \Common\Api\Topic
{
    use \Common\Api\Common;

    public function getRules()
    {
        $rules = parent::getRules();
        return $rules;
    }


}
