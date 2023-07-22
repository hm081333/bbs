<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * App\Models\AuthModel
 *
 * @property-write mixed $sort
 * @method static \Illuminate\Database\Eloquent\Builder|AuthModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AuthModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AuthModel onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AuthModel query()
 * @method static \Illuminate\Database\Eloquent\Builder|AuthModel withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AuthModel withoutTrashed()
 * @mixin \Eloquent
 */
class AuthModel extends BaseModel implements
    AuthenticatableContract,
    //AuthorizableContract,
    //CanResetPasswordContract,
    JWTSubject
{
    use
        //Authorizable,
        //CanResetPassword,
        //MustVerifyEmail,
        //Notifiable,
        Authenticatable;

    // region 重写方法
    public function __construct(array $attributes = [])
    {
        // 添加序列化隐藏的属性
        $this->hidden[] = 'password';
        $this->hidden[] = 'o_pwd';
        parent::__construct($attributes);
    }
    // endregion

    //region JWT相关

    /**
     * 获取将存储在 JWT 主题声明中的标识符。
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * 返回一个键值数组，其中包含要添加到 JWT 的任何自定义声明。
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'account_type' => Str::snake(class_basename(static::class)),
        ];
    }
    //endregion

}
