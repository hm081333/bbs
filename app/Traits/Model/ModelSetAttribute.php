<?php

namespace App\Traits\Model;

use App\Exceptions\Request\BadRequestException;
use App\Utils\Tools;

trait ModelSetAttribute
{
    /**
     * 排序
     *
     * @param string|integer $value
     *
     * @return void
     */
    public function setSortAttribute($value)
    {
        $this->attributes['sort'] = empty($value) || ($value <= 0 || $value > 255) ? 255 : $value;
    }

    /**
     * 编号
     *
     * @param                  $prefix
     *
     * @return void
     */
    public function setSnAttribute($prefix)
    {
        $this->attributes['sn'] = $this->generateSerialNumber($prefix);
    }

    //region 自定义方法

    /**
     * 生成编号
     *
     * @param string $prefix 编号前缀
     * @param string $column 编号字段
     *
     * @return string
     */
    protected function generateSerialNumber(string $prefix, string $column = 'sn')
    {
        $sn = $prefix . date('YmdHis') . Tools::randString(6, 1);
        return static::where($column, $sn)->count() ? $this->GenerateSerialNumber($prefix, $column) : $sn;
    }

    /**
     * 设置操作者信息
     *
     * @desc 设置为当前登录账号信息
     *
     * @return void
     * @throws BadRequestException
     */
    public function setOperator()
    {
        $operator = Tools::auth()->getTokenData();
        $operator_type = $operator['account_type'];
        $operator_id = $operator['account_id'];
        if (empty($operator_type) || empty($operator_id)) throw new BadRequestException('非法操作');
        $this->setAttribute('operator_type', $operator_type);
        $this->setAttribute('operator_id', $operator['account_id']);
    }
    //endregion
}
