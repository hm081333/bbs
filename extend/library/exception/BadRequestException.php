<?php

declare (strict_types=1);

namespace library\exception;

/**
 * BadRequestException 客户端非法请求
 *
 * 客户端非法请求
 *
 * @package     Exception
 */
class BadRequestException extends Exception
{

    public function __construct($message, $code = 0)
    {
        parent::__construct($message, 400 + $code);
    }
}
