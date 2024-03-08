<?php

namespace App\Http\Middleware;

use App\Exceptions\Request\UnauthorizedException;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests;
use Illuminate\Http\Request;

class Authenticate implements AuthenticatesRequests
{
    /**
     * The authentication factory instance.
     *
     * @var Auth
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param Auth $auth
     *
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * 处理传入请求。
     *
     * @param Request     $request
     * @param Closure     $next
     * @param string|null $guard
     *
     * @return mixed
     *
     * @throws AuthenticationException
     */
    public function handle(Request $request, Closure $next, string|null $guard = null)
    {
        $this->authenticate($request, $guard);
        return $next($request);
    }

    /**
     * 确定用户是否登录到任何给定的警卫。
     *
     * @param Request $request
     * @param string|null  $guard
     *
     * @return void
     *
     * @throws UnauthorizedException
     */
    protected function authenticate(Request $request, string|null $guard)
    {
        // if (empty($guards)) {
        //     $guards = [null];
        // }
        //
        // foreach ($guards as $guard) {
        //     dd($this->auth->shouldUse($guard));
        //     $guard = $this->auth->guard($guard);
        //     if ($this->auth->guard($guard)->check()) {
        //         return $this->auth->shouldUse($guard);
        //     }
        // }

        $guard = $this->auth->guard($guard);
        if (!$guard->check() || !$guard->payload()->get('account_type')) $this->unauthenticated();
    }

    /**
     * 处理未经身份验证的用户。
     *
     * @return void
     *
     * @throws UnauthorizedException
     */
    protected function unauthenticated()
    {
        throw new UnauthorizedException('请登录');
    }

}
