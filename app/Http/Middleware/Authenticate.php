<?php

namespace App\Http\Middleware;

use App\Exceptions\Request\BadRequestException;
use App\Exceptions\Request\UnauthorizedException;
use App\Utils\Tools;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;

class Authenticate implements AuthenticatesRequests
{
    /**
     * 处理传入请求。
     *
     * @param Request     $request
     * @param Closure     $next
     * @param string|null $guard
     *
     * @return mixed
     *
     * @throws BadRequestException
     * @throws UnauthorizedException
     * @throws BindingResolutionException
     */
    public function handle(Request $request, Closure $next, string|null $guard = null)
    {
        Tools::auth()->check($guard);
        return $next($request);
    }

}
