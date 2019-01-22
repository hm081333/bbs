<?php

namespace Exception;

/**
 * Exception_BadRequest 客户端非法请求
 *
 * 客户端非法请求
 *
 * @package     Exception
 */
class Exception_BadRequest extends \Exception
{

    public function __construct($message, $code = 0)
    {
        parent::__construct(
            \PhalApi\T('{message}', ['message' => $message]), 400 + $code
        );
    }
}
