<?php

namespace App\Exceptions\Request;

/**
 * 请求异常基础类
 *
 * @package App\Exceptions\Request
 * @class   BaseRequestException
 * @author  oho 2024-03-09
 */
class BaseRequestException extends \App\Exceptions\BaseException
{
    /**
     * 请求错误 异常抛出
     * @param $message
     * @param $code
     */
    public function __construct($message, $code = 0)
    {
        parent::__construct($message, 400 + $code);
    }
}
