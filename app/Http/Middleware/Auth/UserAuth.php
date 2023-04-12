<?php

namespace App\Http\Middleware\Auth;

use App\Exceptions\Request\UnauthorizedException;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAuth
{
    public function handle(Request $request, Closure $next)
    {
        $guard = auth('user');
        if (!$guard->check() || !$guard->payload()->get('account_type')) throw new UnauthorizedException('请登录');

        return $next($request);
    }
}
