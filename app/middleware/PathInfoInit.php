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
        if ($s) {
            // $s = str_replace('.', '/', $s);
            $ss = explode('.', $s);
            if (!empty($ss)) {
                $ss[0] = strtolower($ss[0]);
                $request->setPathinfo(implode('/', $ss));
            }
        }
        return $next($request);
    }

    public function end(Response $response)
    {
        // 回调行为
    }
}
