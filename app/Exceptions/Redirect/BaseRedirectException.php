<?php

namespace App\Exceptions\Redirect;

/**
 * 重定向异常基础类
 *
 * @package App\Exceptions\Redirect
 * @class   BaseRedirectException
 * @author  oho 2024-03-09
 */
class BaseRedirectException extends \App\Exceptions\BaseException
{
    /**
     * 重定向 异常抛出
     * @param $message
     * @param $code
     */
    public function __construct($message, $code = 0)
    {
        parent::__construct($message, 300 + $code);
    }
}
