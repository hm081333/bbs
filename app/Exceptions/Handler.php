<?php

namespace App\Exceptions;

use App\Utils\Bark;
use App\Utils\Tools;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * 未报告的异常类型的列表。
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        \App\Exceptions\BaseException::class,
    ];

    /**
     * 在验证异常时从未闪现的输入的列表。
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * 为应用程序注册异常处理回调。
     *
     * @return void
     */
    public function register()
    {
        // $this->reportable(function (Throwable $e) {
        // });
        $this->renderable(function (Throwable $e, Request $request) {
            DB::rollBack();
            $responseStatusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 200;
            $responseData = [
                'code' => 400,
                'msg' => $e->getMessage(),
            ];
            if ($e instanceof \App\Exceptions\BaseException) {
                $responseData['code'] = $e->getCode();
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\JWTException) {
                $responseData['code'] = '401';
                $responseData['msg'] = '请登录';
            } else if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                //region 抛出的Http异常，404等
                $exception_name = str_replace('HttpException', '', class_basename($e));
                $status_text = preg_replace('/(.)(?=[A-Z])/u', '$1' . ' ', $exception_name);
                $responseData['code'] = $e->getStatusCode();
                $responseData['msg'] = $status_text;
                //endregion
            } else if (Tools::isProduction()) {
                Bark::instance()
                    ->setGroup('Exception')
                    ->setTitle(config('app.name', '') . '系统异常捕获')
                    ->setBody(implode("\n\n", array_merge([
                        get_class($e) . ':' . $e->getCode(),
                        $e->getMessage(),
                    ], array_filter(array_map(fn($trace) => isset($trace['file']) ? str_replace(base_path(), '', $trace['file']) . ':' . $trace['line'] : false, $e->getTrace())))))
                    ->send();
                $responseData['msg'] = '请求错误，请联系管理员。';
            }
            if (Tools::isDebug()) {
                $responseData['error'] = $e->getMessage();
                $responseData['debug'] = [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => Tools::jsonDecode(Tools::jsonEncode($e->getTrace())),
                ];
            }
            if ($request->isJson() || $request->expectsJson()) {
                return response()->json($responseData)->setStatusCode($responseStatusCode);
            } else {
                return response()->view('errors.template', [
                    'statusCode' => $responseData['code'],
                    'statusText' => $responseData['msg'],
                ])->setStatusCode($responseStatusCode);
            }
        });
    }
}
