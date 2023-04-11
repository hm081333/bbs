<?php

namespace App\Traits\Model;

trait ModelGetAttribute
{
    /**
     * 性别名称获取器
     * @return string
     */
    public function getSexNameAttribute($value)
    {
        switch ($this->sex) {
            case 1:
                return '男';
            case 2:
                return '女';
            default:
                return '未知';
        }
    }
}
