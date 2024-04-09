<?php

namespace App\Models\Tieba;

use App\Casts\TimestampCast;
use App\Models\BaseModel;
use App\Models\User\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaiduTieba extends BaseModel
{
    use SoftDeletes;

    protected $casts = [
        'latest' => TimestampCast::class,
        'refresh_time' => TimestampCast::class,
        'no' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function baidu()
    {
        return $this->belongsTo(BaiduId::class, 'baidu_id', 'id');
    }
}
