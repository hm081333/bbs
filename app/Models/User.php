<?php

namespace App\Models;

use App\Casts\Timestamp;

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
        'email_verified_at' => Timestamp::class,
        'birthdate' => Timestamp::class,
        'last_login_time' => Timestamp::class,
        'frozen_time' => Timestamp::class,
    ];

    //endregion

}
