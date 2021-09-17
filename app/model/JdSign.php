<?php
declare (strict_types=1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class JdSign extends Model
{
    //region 模型关联
    public function jdSignItem()
    {
        return $this->hasOne(JdSignItem::class, 'key', 'sign_key');
    }

    //endregion
}
