<?php

namespace App\Traits\Auth;

use App\Exceptions\Request\UnauthorizedException;
use App\Models\Admin\Admin;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Tymon\JWTAuth\JWTGuard;

trait AdminAuth
{

    /**
     * 获取管理员认证
     * @return JWTGuard|Guard
     */
    public function getAdminGuard()
    {
        return auth('admin');
    }

    /**
     * 获取当前登录管理员ID
     * @param bool $thr
     * @return int
     * @throws UnauthorizedException
     */
    public function getAdminId($thr = true)
    {
        $id = $this->getAdminGuard()->id();
        if (!$id && $thr) throw new UnauthorizedException('请登录');
        return $id;
    }

    /**
     * 获取当前登录管理员
     * @param bool $thr
     * @return Admin|Authenticatable
     * @throws UnauthorizedException
     */
    public function getAdmin($thr = true)
    {
        $user = $this->getAdminGuard()->user();
        if (!$user && $thr) throw new UnauthorizedException('请登录');
        return $user;
    }
}
