<?php

namespace App\Traits\Auth;

use App\Exceptions\Request\UnauthorizedException;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\JWTGuard;

trait UserAuth
{

    /**
     * 获取当前登录用户ID
     * @param bool $thr
     * @return int|string|null
     * @throws UnauthorizedException
     */
    public function getUserId($thr = true)
    {
        $auth = $this->getUserGuard();
        if (!$auth->check() && $thr) throw new UnauthorizedException('请登录');
        return $auth->id();
    }

    /**
     * 获取用户认证
     * @return JWTGuard|Guard
     */
    public function getUserGuard()
    {
        return auth('user');
    }

    /**
     * 获取当前登录用户
     * @param bool $thr
     * @return User|Authenticatable|null
     * @throws UnauthorizedException
     */
    public function getUser($thr = true)
    {
        $auth = $this->getUserGuard();
        if (!$auth->check() && $thr) throw new UnauthorizedException('请登录');
        return $auth->user();
    }
}
