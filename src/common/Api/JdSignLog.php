<?php

namespace Common\Api;

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
        $rules['doInfo'] = [
            'jd_user_id' => ['name' => 'jd_user_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '京东用户ID'],
            'open_signs' => ['name' => 'open_signs', 'type' => 'array', 'default' => [], 'desc' => '选择的签到项'],
        ];
        $rules['changeSignStatus'] = [
            'id' => ['name' => 'id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '签到ID'],
            'status' => ['name' => 'status', 'type' => 'enum', 'range' => [0, 1], 'require' => true, 'desc' => '签到状态'],
        ];
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
        $data['where']['user_id'] = $this->session_user['id'];
        $list = $this->Domain_JdSignLog()::getList($data['limit'], $data['offset'], $data['where'], $data['field'], $data['order']);
        array_walk($list['rows'], function (&$value) {
            $value['sign_key_name'] = $this->Domain_JdSignLogItem()->itemKeyName($value['sign_key']);
            return $value;
        });
        return $list;
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

    /**
     * 列表数据 不分页
     * @desc      获取列表数据 不分页
     * @return array    数据列表
     * @throws BadRequestException
     * @exception 400 非法请求，参数传递错误
     */
    public function allListData()
    {
        $data = get_object_vars($this);
        $data['where']['user_id'] = $this->session_user['id'];
        return $this->Domain_JdSignLog()::getListByWhere($data['where'], $data['field'], $data['order']);
    }

    /**
     * 更新 会员选择的 京东签到项目
     * @throws BadRequestException
     */
    public function doInfo()
    {
        $this->Domain_JdSignLog()->doInfo($this->jd_user_id, $this->open_signs);
    }

    /**
     * 更改签到状态
     * @throws BadRequestException
     */
    public function changeSignStatus()
    {
        $this->Domain_JdSignLog()->changeSignStatus($this->id, $this->status);
    }


}
