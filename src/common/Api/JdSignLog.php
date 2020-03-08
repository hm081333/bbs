<?php

namespace Common\Api;

use Common\Domain\JdSignItem;
use Library\Exception\BadRequestException;
use Library\Exception\Exception;
use Library\Traits\Api;

/**
 * 京东签到记录 接口服务类
 * JdSignLog
 * @author LYi-Ho 2018-11-24 16:06:44
 */
class JdSignLog extends Base
{
    use Api;

    public function getRules()
    {
        $rules = parent::getRules();
        return $rules;
    }

    /**
     * 列表数据
     * @desc      获取列表数据
     * @return array    数据列表
     * @throws BadRequestException
     * @exception 400 非法请求，参数传递错误
     */
    public function listData()
    {
        $data = get_object_vars($this);
        $list = $this->Domain_JdSignLog()::getList($data['limit'], $data['offset'], $data['where'], $data['field'], $data['order']);
        array_walk($list['rows'], function (&$value) {
            $value['sign_key_name'] = $this->Domain_JdSignItem()->itemKeyName($value['sign_key']);
            $value['reward_type_name'] = $this->Domain_JdSignLog()->rewardTypeNames($value['reward_type']);
            return $value;
        });
        return $list;
    }

    /**
     * 京东签到项 领域层
     * @return JdSignItem
     * @throws BadRequestException
     */
    protected function Domain_JdSignItem()
    {
        return self::getDomain('JdSignItem');
    }

    /**
     * 京东签到 领域层
     * @return \Common\Domain\JdSignLog
     * @throws BadRequestException
     */
    protected function Domain_JdSignLog()
    {
        return self::getDomain('JdSignLog');
    }

}
