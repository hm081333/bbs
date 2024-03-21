<?php

namespace App\Models\User;

use App\Casts\TimestampCast;
use App\Models\AuthModel;

class User extends AuthModel
{
    //region 类属性
    /**
     * 可批量分配的属性。
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_name',
        'email',
        'o_pwd',
        'password',
    ];

    protected $casts = [
        'email_verified_at' => TimestampCast::class,
        'birthdate' => TimestampCast::class,
        'last_login_time' => TimestampCast::class,
        'frozen_time' => TimestampCast::class,
    ];

    //endregion

    // /**
    //  * 登录日志
    //  *
    //  * @return \Illuminate\Database\Eloquent\Relations\HasMany
    //  */
    // public function loginLog(): \Illuminate\Database\Eloquent\Relations\HasMany
    // {
    //     return $this->hasMany(UserLoginLog::class);
    // }

}
