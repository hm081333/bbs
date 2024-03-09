<?php

namespace App\Utils\Register;

use App\Exceptions\Request\BadRequestException;
use App\Exceptions\Request\UnauthorizedException;
use App\Models\AuthModel;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\Parser;
use Throwable;
use Tymon\JWTAuth\JWTGuard;

class JWTAuth
{
    /**
     * 身份验证工厂实例。
     *
     * @var Auth
     */
    protected $auth;

    /**
     * 身份验证看守器数组
     *
     * @var Guard[]
     */
    protected $guards = [];

    /**
     * Create a new middleware instance.
     *
     * @param Auth $auth
     *
     * @return void
     */
    public function __construct()
    {
        $this->auth = auth();
    }

    /**
     * 返回guard
     *
     * @param string|null $guard_key
     *
     * @return Guard|JWTGuard
     */
    public function guard(string|null $guard_key = null)
    {
        if (!isset($this->guards[$guard_key])) $this->guards[$guard_key] = $this->auth->guard($guard_key);
        return $this->guards[$guard_key];
    }

    /**
     * 处理未经身份验证的用户。
     *
     * @return null
     *
     * @throws UnauthorizedException
     */
    protected function unauthenticated(bool $thr = true)
    {
        if ($thr) throw new UnauthorizedException('请登录');
        return null;
    }

    /**
     * 确定用户是否登录到任何给定的警卫。
     *
     * @param string|null $guard_key
     *
     * @return bool|null
     *
     * @throws BadRequestException
     * @throws UnauthorizedException
     */
    public function check(string|null $guard_key = null, bool $thr = true)
    {
        $guard = $this->guard($guard_key);
        if (!$guard->check() || !$guard->payload()->get('account_type')) return !!$this->unauthenticated($thr);
        if ($guard_key == 'admin') {
            $admin = $guard->user();
            if (!$admin->is_super) {
                if (!$admin->role->permissions()->where('is_interface', 1)->where('component', \request()->route()->uri())->first(['permission_id'])) throw new BadRequestException('权限不足');
            }
        }
        return true;
    }

    /**
     * 获取当前登录用户ID
     *
     * @param string|null $guard
     * @param bool        $thr
     *
     * @return int
     * @throws UnauthorizedException
     */
    public function getId(string|null $guard = null, bool $thr = true): ?int
    {
        $id = $this->guard($guard)->id();
        return $id ?: $this->unauthenticated($thr);
    }

    /**
     * 获取当前登录用户
     *
     * @param string|null $guard
     * @param bool        $thr
     *
     * @return AuthModel|Authenticatable|null
     * @throws UnauthorizedException
     */
    public function getUser(string|null $guard = null, bool $thr = true): AuthModel|Authenticatable|null
    {
        $user = $this->guard($guard)->user();
        return $user ?: $this->unauthenticated($thr);
    }

    /**
     * 获取令牌对应账号ID与账号类型
     *
     * @return array
     * @throws BindingResolutionException
     */
    public function getTokenData(array $data = [])
    {
        $token_parser = app()->make('tymon.jwt.parser');
        $token_str = $token_parser->parseToken();
        if (empty($token_str)) return $data;
        try {
            $JWTParser = new Parser(new JoseEncoder());
            $token = $JWTParser->parse($token_str);
            $claims = $token->claims();
            $data['account_type'] = $claims->get('account_type', '');
            $data['account_id'] = $claims->get('sub', 0);
        } catch (Throwable $e) {
            Log::error('获取令牌信息失败');
            Log::error($token_str);
            Log::error($e);
        }
        return $data;
    }
}
