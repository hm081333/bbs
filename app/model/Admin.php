<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Admin extends Model
{
    public function getAdminInfo()
    {
        if ($this->isEmpty()) return [];
        return [
            'user_name' => $this->getAttr('user_name'),
            'auth' => $this->getAttr('auth'),
            'status' => $this->getAttr('status'),
        ];
    }
}
