<?php

namespace Bbs\Api;

/**
 * 科目接口服务类
 * Subject
 * @author LYi-Ho 2018-11-24 16:06:44
 */
class Subject extends \Common\Api\Subject
{
    public function getRules()
    {
        $rules = parent::getRules();
        return $rules;
    }


}
