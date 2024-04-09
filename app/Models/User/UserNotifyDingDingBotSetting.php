<?php

namespace App\Models\User;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserNotifyDingDingBotSetting extends BaseModel
{
    protected $casts = [
        'enable' => 'boolean',
    ];
}
