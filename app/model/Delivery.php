<?php
declare (strict_types=1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Delivery extends Model
{
    private $status = [
        0 => '在途',
        1 => '揽件',
        2 => '疑难',
        3 => '签收',
        4 => '退签',
        5 => '派件',
        6 => '退回',
    ];

    //region 获取器

    /**
     * 物流状态
     * @param $value
     * @param $data
     * @return array|mixed|string|string[]
     */
    public function getStateNameAttr($value, $data)
    {
        return $this->getStateName($data['state']);
    }

    /**
     * 历史查询信息
     * @param $value
     * @return mixed
     */
    public function getLastMessageAttr($value)
    {
        return unserialize($value);
    }
    //endregion

    //region 修改器
    /**
     * 历史查询信息
     * @param $value
     * @return string
     */
    public function setLastMessageAttr($value)
    {
        return serialize($value);
    }
    //endregion

    /**
     * 获取物流状态码对应的物流状态信息
     * @param bool $state
     * @return array|mixed|string
     */
    public function getStateName($state = false)
    {
        if ($state === false) return $this->status;
        return $this->status[$state] ?? '不存在';
    }
}
