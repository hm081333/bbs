<?php
declare (strict_types=1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Chat extends Model
{
    //region 获取器
    public function getUserIdsAttr($value)
    {
        return is_array($value) ? $value : explode(',', $value);
    }
    //endregion
}
