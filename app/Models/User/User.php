<?php

namespace App\Models\User;

use App\Casts\TimestampCast;
use App\Models\AuthModel;
use App\Models\Tieba\BaiduId;
use App\Models\Tieba\BaiduTieba;
use App\Models\WeChat\WechatOfficialAccountUser;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends AuthModel
{
    use SoftDeletes;

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

    //region 模型关联
    /**
     * 百度账号
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function baiduIds()
    {
        return $this->hasMany(BaiduId::class, 'user_id', 'id');
    }

    /**
     * 关联贴吧
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tiebas()
    {
        return $this->hasMany(BaiduTieba::class, 'user_id', 'id');
    }

    public function wechatOfficialAccount()
    {
        return $this->hasOne(WechatOfficialAccountUser::class);
    }
    //endregion

}
