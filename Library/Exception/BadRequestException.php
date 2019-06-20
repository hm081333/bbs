<?php

namespace Library\Exception;

/**
 * BadRequestException 客户端非法请求
 *
 * 客户端非法请求
 *
 * @package     Exception
 */
class BadRequestException extends \PhalApi\Exception
{

    public function __construct($message, $code = 0)
    {
        parent::__construct(
            \PhalApi\T('{message}', ['message' => $message]), 400 + $code
        );
    }
}
