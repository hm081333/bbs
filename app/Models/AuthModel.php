<?php

namespace App\Models;

use App\Exceptions\Request\BadRequestException;
use App\Exceptions\Server\InternalServerErrorException;
use App\Utils\Tools;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Contracts\JWTSubject;

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

    /**
     * 账号类型
     *
     * @var null|string
     */
    protected ?string $account_type = null;

    // region 重写方法

    public function __construct(array $attributes = [])
    {
        if (!isset($this->account_type)) $this->account_type = Str::snake(class_basename(static::class));
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
            'account_type' => $this->account_type,
        ];
    }

    //endregion

    /**
     * 登录日志
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function loginLog(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        $login_log_class = static::class . 'LoginLog';
        if (!class_exists($login_log_class)) throw new InternalServerErrorException('请联系管理员注册登录日志');
        return $this->hasMany($login_log_class);
    }

    /**
     * 登录，返回token（不传入密码会直接登录）
     *
     * @param string|false|null $password 账号密码
     *
     * @return string
     * @throws BadRequestException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function login(string|false|null $password = false): string
    {
        if ($password === false) {
            $user_password = $this->getAuthPassword();
            if (empty($user_password) || !Hash::isHashed($user_password)) throw new BadRequestException('账号或密码不正确');
            if (empty($password)) throw new BadRequestException('请输入密码');
            // 密码需经过md5加密
            if (strlen($password) != 32) $password = md5($password);
            // md5加密后的密码全大写
            $password = strtoupper($password);
            if (!Hash::check($password, $user_password)) throw new BadRequestException('密码不正确');
        }
        $this->loginLog()->create([
            // 'user_id' => Tools::auth()->id($this->account_type),
            'user_id' => $this->getAuthIdentifier(),
        ]);
        return Tools::auth()->login($this->account_type, $this);
    }

}
