<?php

namespace App\Models\WeChat;

use App\Models\BaseModel;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Casts\AsCollection;

class WechatOfficialAccountUser extends BaseModel
{
    protected $casts = [
        // 'tagid_list' => 'array',
        'tagid_list' => AsCollection::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
