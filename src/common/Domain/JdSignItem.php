<?php
/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2018-11-26
 * Time: 11:14:57
 */

namespace Common\Domain;

/**
 * 京东签到项 领域层
 * JdSignItem
 * @package Common\Domain
 * @author  LYi-Ho 2018-11-26 11:14:57
 */
class JdSignItem
{
    use Common;

    public static function statusNames($status = false)
    {
        $names = [
            0 => '关闭',
            1 => '启用',
        ];
        if ($status === false) {
            return $names;
        }
        return $names[$status];
    }


}
