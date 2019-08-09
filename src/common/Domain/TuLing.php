<?php
/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2018-11-26
 * Time: 11:14:57
 */

namespace Common\Domain;


/**
 * 图灵 领域层
 * Class TuLing
 * @package Common\Domain
 * @author  LYi-Ho 2018-11-26 11:14:57
 */
class TuLing
{
    use Common;

    /**
     * @param $question string 问题
     * @return array
     */
    public static function get($question)
    {
        $tuling_url = 'http://www.tuling123.com/openapi/api';
        $setting = Setting::getSetting('tuling');
        $rs = \Common\DI()->curl->post($tuling_url, ['key' => $setting['api_key'], 'info' => $question]);
        $rs = json_decode($rs, true);
        return $rs;
    }
}
