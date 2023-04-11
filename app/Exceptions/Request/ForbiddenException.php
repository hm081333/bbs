<?php

namespace App\Exceptions\Request;


class ForbiddenException extends Exception
{
    /**
     * 禁止访问 异常抛出
     * @param $message
     * @param $code
     */
    public function __construct($message)
    {
        parent::__construct($message, 3);
    }
}
