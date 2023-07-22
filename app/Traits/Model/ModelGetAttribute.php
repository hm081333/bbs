<?php

namespace App\Traits\Model;

use App\Models\Area;
use App\Models\OptionItem;

trait ModelGetAttribute
{
    //region 获取器
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
    //endregion

    //region 自定义方法
    /**
     * 获取选项值文本
     * @param string $key 待转换字段
     * @return string
     */
    protected function getOptionItemText(string $key): string
    {
        $id = $this->getAttribute($key);
        return empty($id) ? '' : OptionItem::getValue($id);
    }

    /**
     * 获取区域文本
     * @param string $key 待转换字段
     * @param string $label_key 获取值字段
     * @return string
     */
    protected function getAreaText(string $key, string $label_key = 'name'): string
    {
        $id = $this->getAttribute($key);
        return empty($id) ? '' : Area::getValue($id, $label_key);
    }
    //endregion
}
