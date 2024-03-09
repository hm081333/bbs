<?php

namespace App\Exceptions\Redirect;

class RedirectException extends BaseRedirectException
{
    /**
     * 重定向，需要进一步的操作以完成请求
     * @param $message
     */
    public function __construct($message)
    {
        parent::__construct($message, 0);
    }
}
