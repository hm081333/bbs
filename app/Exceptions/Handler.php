<?php

namespace App\Exceptions;

use App\Utils\Bark;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * 未报告的异常类型的列表。
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        \App\Exceptions\Exception::class,
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
        $this->reportable(function (Throwable $e) {
            //
        });
        $this->renderable(function (Throwable $e, Request $request) {
            //region 抛出的Http异常，404等
            if ($e instanceof HttpException) {
                $exception_name = str_replace('HttpException', '', class_basename($e));
                $status_text = preg_replace('/(.)(?=[A-Z])/u', '$1' . ' ', $exception_name);
                if ($request->isJson() || $request->expectsJson()) {
                    return response()->json($e->getStatusCode() . ' ' . $status_text)->setStatusCode($e->getStatusCode());
                } else {
                    return response()->view('errors.template', [
                        'statusCode' => $e->getStatusCode(),
                        'statusText' => $status_text,
                    ])->setStatusCode($e->getStatusCode());
                }
            }
            //endregion
            DB::rollBack();
            if ($request->isJson() || $request->expectsJson()) {
                $data = [
                    'code' => $e->getCode(),
                    'data' => null,
                    'msg' => $e->getMessage(),
                ];
                if (!($e instanceof \App\Exceptions\Exception) && config('app.env') == 'production') {
                    Bark::instance()
                        ->setGroup('Exception')
                        ->setTitle('招生平台异常捕获')
                        ->setBody(implode("\n\n", array_merge([
                            get_class($e) . ':' . $e->getCode(),
                            $e->getMessage(),
                        ], array_filter(array_map(function ($trace) {
                            return isset($trace['file']) ? str_replace(base_path(), '', $trace['file']) . ':' . $trace['line'] : false;
                        }, $e->getTrace())))))
                        ->send();
                    $data['msg'] = '请求错误，请联系管理员。';
                }
                if (config('app.debug')) {
                    $data['debug'] = [
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => json_decode(json_encode($e->getTrace()), true),
                    ];
                }
                return response()->json($data)->setStatusCode(method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 200);
            }
        });
    }
}
