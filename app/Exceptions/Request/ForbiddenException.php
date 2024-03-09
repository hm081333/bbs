<?php

namespace App\Exceptions\Request;


class ForbiddenException extends BaseRequestException
{
    /**
     * 禁止访问 异常抛出
     * @param $message
     */
    public function __construct($message)
    {
        parent::__construct($message, 3);
    }
}
