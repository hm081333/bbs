<?php

namespace App\Exceptions;

/**
 * 异常基础类
 *
 * @package App\Exceptions
 * @class   BaseException
 * @author  oho 2024-03-09
 */
class BaseException extends \Exception
{
    /**
     * 基础 异常抛出
     * @param $message
     * @param $code
     */
    public function __construct($message, $code = 0)
    {
        parent::__construct($message, $code);
    }
}
