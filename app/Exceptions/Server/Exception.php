<?php

namespace App\Exceptions\Server;


class Exception extends \App\Exceptions\Exception
{
    /**
     * 服务器错误 异常抛出
     * @param $message
     * @param $code
     */
    public function __construct($message, $code = 0)
    {
        parent::__construct($message, 500 + $code);
    }
}
