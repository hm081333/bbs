<?php


namespace Common\Api;

/**
 * 上传接口服务类
 * @ignore
 * @author LYi-Ho 2018-11-24 16:57:36
 */
class Upload extends Base
{
    use Common;

    /**
     * 接口参数规则
     * @return array
     */
    public function getRules()
    {
        $rules = parent::getRules();
        return $rules;
    }


}
