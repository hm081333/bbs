<?php

declare (strict_types=1);

namespace library\exception;

/**
 * InternalServerErrorException 服务器运行异常错误
 *
 * 服务器运行异常错误
 *
 * @package     Exception
 */
class InternalServerErrorException extends Exception
{

    public function __construct($message, $code = 0)
    {
        parent::__construct($message, 500 + $code);
    }
}
