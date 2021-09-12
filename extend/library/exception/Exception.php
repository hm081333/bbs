<?php

declare (strict_types=1);

namespace library\exception;

/**
 * Exception 普通抛出
 *
 * 普通抛出 code 200
 *
 * @package     Exception
 */
class Exception extends \think\Exception
{
    public function __construct($message, $code = 0)
    {
        parent::__construct($message, $code < 100 ? 200 + $code : $code);
    }
}
