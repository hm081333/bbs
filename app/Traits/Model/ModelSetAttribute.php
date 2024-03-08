<?php

namespace App\Traits\Model;

use App\Exceptions\Request\BadRequestException;
use App\Utils\Tools;
use Illuminate\Support\Facades\Auth;

trait ModelSetAttribute
{
    /**
     * 排序
     * @param string|integer $value
     * @return void
     */
    public function setSortAttribute($value)
    {
        $this->attributes['sort'] = empty($value) || ($value <= 0 || $value > 255) ? 255 : $value;
    }

    /**
     * 编号
     * @param                  $prefix
     * @return void
     */
    public function setSnAttribute($prefix)
    {
        $this->attributes['sn'] = $this->generateSerialNumber($prefix);
    }

    //region 自定义方法
    /**
     * 生成编号
     * @param string $prefix 编号前缀
     * @param string $column 编号字段
     * @return string
     */
    protected function generateSerialNumber(string $prefix, string $column = 'sn')
    {
        $sn = $prefix . date('YmdHis') . Tools::randString(6, 1);
        return static::where($column, $sn)->count() ? $this->GenerateSerialNumber($prefix, $column) : $sn;
    }

    /**
     * 设置操作者信息
     * @param string $type 操作来源类型
     * @return void
     * @throws BadRequestException
     */
    public function setOperator($type = false)
    {
        if (!$this->getAttribute('operator_type')) {
            if ($type) {
                $this->setAttribute('operator_type', $type);
            } else {
                $route_names = explode('.', request()->route()->getName());
                $this->setAttribute('operator_type', $route_names[0]);
            }
        }
        switch ($this->getAttribute('operator_type')) {
            case 'admin':
                $operator_id = auth('admin')->id();
                break;
            case 'home':
            case 'user':
                $operator_id = auth('user')->id();
                break;
            default:
                throw new BadRequestException('非法操作');
        }
        $this->setAttribute('operator_id', $operator_id);
    }
    //endregion
}
