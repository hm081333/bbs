<?php

namespace Common\Api;

/**
 * 微信公众平台 接口服务类
 * @ignore
 * @author LYi-Ho 2018-11-26 11:14:57
 */
class WeChatPublicPlatform extends Base
{
    /**
     * 接口参数规则
     * @return array
     */
    public function getRules()
    {
        $rules = parent::getRules();
        $rules['getOpenIdCode'] = $rules['getOpenIdCodeUrl'] = [
            'redirect' => ['name' => 'redirect', 'type' => 'string', 'require' => true, 'desc' => '重定向地址'],
            'scope' => ['name' => 'scope', 'type' => 'string', 'default' => 'snsapi_base', 'desc' => '授权方式'],
        ];
        $rules['getOpenId'] = [
            'code' => ['name' => 'code', 'type' => 'string', 'require' => true, 'desc' => '授权码'],
        ];
        return $rules;
    }

    /**
     * @return \Common\Domain\WeChatPublicPlatform
     * @throws \Library\Exception\BadRequestException
     */
    public function domain()
    {
        return self::getDomain();
    }

    /**
     * 重定向获取授权码
     * @throws \Library\Exception\BadRequestException
     */
    public function getOpenIdCode()
    {
        $this->domain()->getOpenIdCode($this->redirect, $this->scope);
    }

    /**
     * 获取授权码的链接
     * @return string
     * @throws \Library\Exception\BadRequestException
     */
    public function getOpenIdCodeUrl()
    {
        return $this->domain()->getOpenIdCodeUrl($this->redirect, $this->scope);
    }

    /**
     * 获取OpenId
     * @return mixed
     * @throws \Library\Exception\BadRequestException
     * @throws \Library\Exception\Exception
     * @throws \PhalApi\Exception\InternalServerErrorException
     */
    public function getOpenId()
    {
        return $this->domain()->getOpenId($this->code);
    }

}
