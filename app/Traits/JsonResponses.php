<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait JsonResponses
{
    /**
     * 响应一个错误信息
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    public function error(string $message, int $code = 400): JsonResponse
    {
        return new JsonResponse([
            'code' => $code,
            'msg' => $message,
        ]);
    }

    /**
     * 响应一个成功消息
     * @param string $message
     * @param mixed|null $data
     * @return JsonResponse
     */
    public function success(string $message = '', mixed $data = null): JsonResponse
    {
        return new JsonResponse([
            'code' => 200,
            'msg' => $message,
            'data' => $data,
        ]);
    }
}
