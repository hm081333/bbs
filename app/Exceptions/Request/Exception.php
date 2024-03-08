<?php

namespace App\Exceptions\Request;


class Exception extends \App\Exceptions\Exception
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
