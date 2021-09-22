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
}
