<?php

namespace App\Exceptions\Redirect;

class Exception extends \App\Exceptions\Exception
{
    /**
     * 重定向 异常抛出
     * @param $message
     * @param $code
     */
    public function __construct($message, $code = 0)
    {
        parent::__construct($message, 300 + $code);
    }
}
