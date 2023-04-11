<?php

namespace App\Exceptions\Request;


class NotFoundException extends Exception
{
    /**
     * 未找到 异常抛出
     * @param $message
     * @param $code
     */
    public function __construct($message)
    {
        parent::__construct($message, 4);
    }
}
