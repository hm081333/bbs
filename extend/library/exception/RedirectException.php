<?php

declare (strict_types=1);

namespace library\exception;

/**
 * RedirectException 重定向
 *
 * 重定向，需要进一步的操作以完成请求
 *
 * @package     Exception
 */
class RedirectException extends Exception
{

    public function __construct($message, $code = 0)
    {
        parent::__construct($message, 300 + $code);
    }
}
