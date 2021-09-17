<?php
declare (strict_types=1);

namespace app\middleware;

use app\Request;
use Closure;
use library\exception\BadRequestException;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Response;

class Auth
{

    /**
     * 处理请求
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     * @throws BadRequestException
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function handle(Request $request, Closure $next)
    {
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
        switch (strtolower(app('http')->getName())) {
            case 'bbs':
                // 获取会员登录状态
                $request->getCurrentUser();
                break;
            case 'sign':
                // 获取会员登录状态
                $request->getCurrentUser(true);
                break;
            case 'admin':
                // 获取管理员登录状态
                $request->getCurrentAdmin(true);
                break;
            case 'common':
                // 获取会员登录状态
                $request->getCurrentUser();
                // 获取管理员登录状态
                $request->getCurrentAdmin();
                break;
        }
        return $next($request);
    }

    public function end(Response $response)
    {
        // 回调行为
    }
}
