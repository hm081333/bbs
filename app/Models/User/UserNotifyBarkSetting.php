<?php

namespace App\Models\User;

use App\Models\BaseModel;

class UserNotifyBarkSetting extends BaseModel
{
    protected $casts = [
        'enable' => 'boolean',
    ];
}
