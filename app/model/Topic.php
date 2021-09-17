<?php
declare (strict_types=1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Topic extends Model
{

    //region 获取器
    /**
     * 文本内容处理
     * @param $value
     * @return string
     */
    public function getDetailAttr($value)
    {
        return htmlspecialchars_decode($value);
    }
    //endregion

    //region 修改器
    /**
     * 文本内容处理
     * @param $value
     * @return string
     */
    public function setDetailAttr($value)
    {
        // HTML文本转为实体
        return htmlspecialchars($value);
    }
    //endregion
}
