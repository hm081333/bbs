<?php
declare (strict_types=1);

namespace app\middleware;

use app\Request;
use Closure;
use think\Response;

class Auth
{
    /**
     * 处理请求
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next)
    {
        // dump($request);
        // die;
        $auth = $request->header('auth', '');
        foreach (explode('|', urldecode($auth)) as $item) {
            $key = substr($item, 0, 32);
            $value = substr($item, 32);
            if ($key == config('app.admin_token')) {
                $request->admin_token = $value;
            } else if ($key == config('app.user_token')) {
                $request->user_token = $value;
            }
        }
        return $next($request);
    }

    public function end(Response $response)
    {
        // 回调行为
    }
}
