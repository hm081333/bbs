<?php

namespace library\facade;

use think\Facade;

/**
 * @see \library\Serialize
 * @package facade
 * @mixin \library\Serialize
 * @method static string get($url, $timeoutMs = 5000) GET方式的请求
 * @method static mixed post($url, $data, $timeoutMs = 5000) POST方式的请求
 */
class CUrl extends Facade
{
    protected static function getFacadeClass()
    {
        return 'library\Serialize';
    }
}
