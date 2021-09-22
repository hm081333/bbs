<?php
declare (strict_types=1);

namespace app\middleware;

use app\Request;
use Closure;
use think\Response;

class PathInfoInit
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
        $s = $request->post('s', $request->get('s', $request->param('s', '')));
        $s = str_replace('.', '/', $s);
        // dump($s);
        $request->setPathinfo($s);
        // dump($request);
        // die;
        return $next($request);
    }

    public function end(Response $response)
    {
        // 回调行为
    }
}
