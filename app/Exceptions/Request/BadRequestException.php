<?php

namespace App\Exceptions\Request;


class BadRequestException extends Exception
{
    /**
     * 请求错误 异常抛出
     * @param $message
     */
    public function __construct($message)
    {
        parent::__construct($message, 0);
    }
}
