<?php

namespace App\Exceptions\Request;


class UnauthorizedException extends BaseRequestException
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
