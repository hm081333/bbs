<?php

/**
 * Exception_InternalServerError 服务器运行异常错误
 *
 * 服务器运行异常错误
 *
 * @package     Exception
 */
class Exception_InternalServerError extends \Exception
{

    public function __construct($message, $code = 0)
    {
        parent::__construct(
            \PhalApi\T('{message}', ['message' => $message]), 500 + $code
        );
    }
}
