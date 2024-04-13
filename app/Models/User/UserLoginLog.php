<?php

namespace App\Models\User;


use App\Models\BaseModel;
use App\Utils\Tools;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserLoginLog extends BaseModel
{
    protected $fillable = [
        'user_id',
        'quit_time',
    ];

    protected static function booted()
    {
        static::creating(function (UserLoginLog $model) {
            $model->setAttribute('ip', request()->ip());
            $model->setAttribute('user_agent', request()->userAgent());
            $model->setAttribute('device_type', request()->getDeviceType());
        });
    }

    /**
     * 关联用户
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
