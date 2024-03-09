<?php

namespace App\Exceptions\Server;

/**
 * 服务异常基础类
 *
 * @package App\Exceptions\Server
 * @class   BaseServerException
 * @author  oho 2024-03-09
 */
class BaseServerException extends \App\Exceptions\BaseException
{
    /**
     * 服务器错误 异常抛出
     * @param $message
     * @param $code
     */
    public function __construct($message, $code = 0)
    {
        parent::__construct($message, 500 + $code);
    }
}
