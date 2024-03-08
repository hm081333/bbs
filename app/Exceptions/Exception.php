<?php

namespace App\Exceptions;

class Exception extends \Exception
{
    /**
     * 基础 异常抛出
     * @param $message
     * @param $code
     */
    public function __construct($message, $code = 400)
    {
        parent::__construct($message, $code);
    }
}
