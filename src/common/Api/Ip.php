<?php


namespace Common\Api;

use Library\Traits\Api;

/**
 * IP地址 接口服务类
 * @ignore
 * @author LYi-Ho 2018-11-24 16:57:36
 */
class Ip extends Base
{
    use Api;

    /**
     * 接口参数规则
     * @return array
     */
    public function getRules()
    {
        $rules = parent::getRules();
        $rules['getInfo'] = [
            'ip' => ['name' => 'ip', 'type' => 'string', 'default' => '', 'desc' => '需要查询的IP地址'],
        ];
        return $rules;
    }

    /**
     * 获取IP详细信息
     * @return array
     */
    public function getInfo()
    {
        $data = get_object_vars($this);
        self::getModel();
        return static::getDomain()::getIPInfo($data);

    }


}
