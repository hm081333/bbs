<?php

namespace App\Http\Middleware;

use App\Utils\Tools;
use Closure;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\Parser;

class AccessLog
{
    /**
     * 获取令牌对应账号ID
     * @param $data
     * @return array
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    private function getAuthId($data)
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
        } catch (Exception $e) {
            Log::error('获取令牌信息失败');
            Log::error($token_str);
            Log::error($e);
            // throw $e;
        }
        return $data;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $begin_time = microtime(true);
        /* @var $response Response */
        $response = $next($request);
        if (in_array(strtolower($request->method()), ['options'])) return $response;
        $end_time = microtime(true);

        try {
            if (!empty($request->route()) && $request->route()->uri() == '{fallbackPlaceholder}') {
                $responseData = 'fallbackPlaceholder';
            } else if ($response instanceof RedirectResponse) {
                $responseData = 'Redirect:' . $response->getTargetUrl();
            } else {
                $responseData = $response->getContent();
            }
            $data = [
                'method' => strtolower($request->method()),
                'url' => rtrim(Tools::url(), '/'),
                'path_info' => $request->getPathInfo(),
                'client_ip' => $request->ip(),
                'params' => json_encode((object)$request->input(), true),
                'get' => json_encode((object)$request->query(), true),
                'post' => json_encode((object)$request->post(), true),
                'headers' => json_encode((object)array_map(function ($header) {
                    return implode(',', $header);
                }, $request->header()), true),
                'user_agent' => $request->userAgent(),
                'response' => $responseData,
                'request_time' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']),
            ];
            // 从index.php到返回响应耗时
            // $data['api_consumed'] = (float)bcmul(bcsub($end_time, LARAVEL_START, 5), 1000, 2);
            $data['api_consumed'] = (float)bcmul(bcsub($end_time, $begin_time, 5), 1000, 2);
            // 从请求到返回响应耗时
            $data['request_consumed'] = (float)bcmul(bcsub($end_time, Tools::time(true), 5), 1000, 2);
            $data = $this->getAuthId($data);
            \App\Models\Mongodb\AccessLog::create($data);
        } catch (Exception $e) {
            Log::error('访问日志写入MongoDB失败');
            Log::error($e);
            // throw $e;
        }
        return $response;
    }
}
