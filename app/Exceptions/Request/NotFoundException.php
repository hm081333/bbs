<?php

namespace App\Exceptions\Request;


class NotFoundException extends BaseRequestException
{
    /**
     * 未找到 异常抛出
     * @param $message
     */
    public function __construct($message)
    {
        parent::__construct($message, 4);
    }
}
