<?php
declare (strict_types=1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Friend extends Model
{
    public function friend()
    {
        return $this->belongsTo(User::class, 'friend_id', 'id');
    }

    /**
     * 好友状态 名称
     * @param bool|int $status
     * @return string|string[]
     */
    public static function friendStatusName($status = false)
    {
        $names = [
            0 => '非好友',
            1 => '对方非好友',
            2 => '非对方好友',
            3 => '好友',
            4 => '已拉黑',
            5 => '对方已拉黑',
        ];
        if ($status != false) {
            return $names[$status] ?? '';
        }
        return $names;
    }
}
