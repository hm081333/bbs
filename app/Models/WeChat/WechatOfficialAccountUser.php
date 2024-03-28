<?php

namespace App\Models\WeChat;

use App\Models\BaseModel;
use App\Models\User\User;

class WechatOfficialAccountUser extends BaseModel
{

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
