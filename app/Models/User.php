<?php

namespace App\Models;

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
        'password',
    ];

    //endregion

}
