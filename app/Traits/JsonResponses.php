<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait JsonResponses
{
    /**
     * 响应一个错误信息
     * @param string $message
     * @param int    $code
     * @return JsonResponse
     */
    public function error($message, $code = 400)
    {
        return response()->json([
            'code' => $code,
            'msg' => $message,
        ]);
    }

    /**
     * 响应一个成功消息
     * @param     $data
     * @param int $code
     * @return JsonResponse
     */
    public function success($message = '', $data = [])
    {
        return response()->json([
            'code' => 200,
            'msg' => $message,
            'data' => $data,
        ]);
    }
}
