<?php

namespace App\Exceptions\Server;

class InternalServerErrorException extends Exception
{
    /**
     * 服务器运行异常错误
     * @param $message
     */
    public function __construct($message)
    {
        parent::__construct($message, 0);
    }
}
