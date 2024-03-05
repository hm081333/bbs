<?php

namespace App\Models;

use App\Casts\Timestamp;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $user_name 用户名
 * @property string|null $nick_name 昵称
 * @property string|null $real_name 真实名称
 * @property string|null $mobile 用户手机
 * @property string|null $email 邮箱
 * @property \Carbon\Carbon|null $email_verified_at 邮箱验证时间
 * @property string|null $previous_avatar 上一张头像
 * @property string|null $avatar 头像
 * @property int $sex 性别 0保密1男2女
 * @property \Carbon\Carbon|null $birthdate 出生日期
 * @property \Carbon\Carbon|null $last_login_time 最后登录时间
 * @property int $status 状态 1正常 2冻结
 * @property \Carbon\Carbon|null $frozen_time 冻结时间
 * @property string|null $open_id 微信唯一ID
 * @property string $password 密码
 * @property string|null $o_pwd 原始密码
 * @property string|null $remember_token
 * @property \Carbon\Carbon|null $created_at 创建时间
 * @property \Carbon\Carbon|null $updated_at 更新时间
 * @property \Carbon\Carbon|null $deleted_at 删除时间
 * @property-read string $sex_name
 * @property-write mixed $sn
 * @property-write mixed $sort
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBirthdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFrozenTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastLoginTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNickName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereOPwd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereOpenId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePreviousAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRealName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUserName($value)
 * @mixin \Eloquent
 */
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
