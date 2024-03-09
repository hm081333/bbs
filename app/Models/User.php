<?php

namespace App\Models;

use App\Casts\TimestampCast;

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

}
