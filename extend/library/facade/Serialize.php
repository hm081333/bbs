<?php

namespace library\facade;

use think\Facade;

/**
 * @see \library\Serialize
 * @package facade
 * @mixin \library\Serialize
 * @method static string encrypt($data, $igbinary = true) 序列化
 * @method static mixed decrypt($data, $igbinary = true) 反序列化
 */
class Serialize extends Facade
{
    protected static function getFacadeClass()
    {
        return 'library\Serialize';
    }
}
