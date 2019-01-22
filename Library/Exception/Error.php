<?php

/**
 * Exception_Error 普通抛出
 *
 * 普通抛出 code 200
 *
 * @package     Exception
 */
class Exception_Error extends \Exception
{

    public function __construct($message, $code = 0)
    {
        parent::__construct(
            \PhalApi\T('{message}', ['message' => $message]), 200 + $code
        );
    }
}
