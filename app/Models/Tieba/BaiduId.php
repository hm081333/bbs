<?php

namespace App\Models\Tieba;

use App\Casts\TimestampCast;
use App\Models\BaseModel;
use App\Models\User\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaiduId extends BaseModel
{
    use SoftDeletes;

    protected $casts = [
        'refresh_time' => TimestampCast::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'user_id');
    }

    public function tieba()
    {
        return $this->hasMany(\App\Models\Tieba\BaiduTieba::class, 'baidu_id', 'id');
    }
}
