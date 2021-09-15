<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class JdSignItem extends Model
{
    public function getDisabledAttr($value,$data)
    {
        return $data['status'] == 0;
    }
}
