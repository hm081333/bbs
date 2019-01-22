<?php


namespace Common\Api;

/**
 * IP地址 接口服务类
 * @ignore
 * @author LYi-Ho 2018-11-24 16:57:36
 */
class Ip extends Base
{
    use Common;

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

    public function getInfo()
    {
        $data = get_object_vars($this);
        return self::getDomain()::getIPInfo($data);

    }


}
