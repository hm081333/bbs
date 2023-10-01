<?php

namespace App\Exceptions\Request;


class UnauthorizedException extends Exception
{
    /**
     * 未登录 异常抛出
     * @param $message
     */
    public function __construct($message)
    {
        parent::__construct($message, 1);
    }
}
