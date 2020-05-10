<?php


namespace Common\Api;

use Library\Crypt\RSA\MultiPub2PriCrypt;
use Library\Exception\BadRequestException;
use Library\Exception\InternalServerErrorException;

/**
 * IP地址 接口服务类
 * @ignore
 * @author LYi-Ho 2018-11-24 16:57:36
 */
class Ip extends Base
{
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
     * IP地址 领域层
     * @return \Common\Domain\Ip
     * @throws BadRequestException
     */
    public function Domain_Ip()
    {
        return self::getDomain();
    }

    /**
     * 获取IP详细信息
     * @return array
     * @throws BadRequestException
     * @throws InternalServerErrorException
     */
    public function getInfo()
    {
        return $this->Domain_Ip()->getIPInfo($this->ip);

    }


}
