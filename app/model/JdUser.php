<?php
declare (strict_types=1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class JdUser extends Model
{
    protected $status = [
        0 => '未启用',
        1 => '正常',
        2 => '已过期',
    ];

    //region 获取器
    public function getStatusNameAttr($value, $data)
    {
        return $this->statusNames($data['status']);
    }

    //endregion

    //region 模型关联
    public function jdSign()
    {
        return $this->hasMany(JdSign::class, 'jd_user_id', 'id');
    }

    //endregion

    public function statusNames($status = false)
    {
        if ($status === false) return $this->status;
        return $this->status[$status] ?? '未定义';
    }
}
