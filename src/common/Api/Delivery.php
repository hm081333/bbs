<?php

namespace Common\Api;

use Library\Traits\Api;

/**
 * 物流信息 接口服务类
 * Delivery
 * @author LYi-Ho 2018-11-24 16:06:44
 */
class Delivery extends Base
{
    use Api;

    public function getRules()
    {
        $rules = parent::getRules();
        $rules['doInfo'] = [
            'id' => ['name' => 'id', 'type' => 'string', 'require' => true, 'default' => 0, 'desc' => 'ID'],
            'sn' => ['name' => 'sn', 'type' => 'string', 'require' => true, 'desc' => '快递单号'],
            'code' => ['name' => 'code', 'type' => 'string', 'require' => true, 'desc' => '物流公司编码'],
            'memo' => ['name' => 'memo', 'type' => 'string', 'require' => true, 'desc' => '备注'],
        ];
        return $rules;
    }

    /**
     * 物流详情数据
     * @desc      获取物流详情数据
     * @return array    数据数组
     * @exception 400 非法请求，参数传递错误
     */
    public function infoData()
    {
        // $data=get_object_vars($this);
        return self::getDomain()::getDeliveryInfo($this->id);
    }

    public function doInfo()
    {
        $data = get_object_vars($this);
        self::getDomain()::doInfo($data);
    }

}
