<?php

namespace Exception;

/**
 * Exception 普通抛出
 *
 * 普通抛出 code 200
 *
 * @package     Exception
 */
class Exception extends \PhalApi\Exception
{

    public function __construct($message, $code = 0)
    {
        parent::__construct(
            \PhalApi\T('{message}', ['message' => $message]), 200 + $code
        );
    }
}
