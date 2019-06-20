<?php
/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2018-11-26
 * Time: 11:14:57
 */

namespace Common\Domain;


/**
 * 物流公司 领域层
 * Class Logistics
 * @package Common\Domain
 * @author  LYi-Ho 2018-11-26 11:14:57
 */
class Logistics
{
    use Common;

    /**
     * 请求快递100 获取物流信息
     * @param string $code 物流公司代号
     * @param string $sn   物流编号
     * @return array
     */
    public static function queryLogistics(string $code, string $sn)
    {
        $result = self::DI()->curl->get('http://www.kuaidi100.com/query?type=' . $code . '&postid=' . $sn);
        return json_decode($result, true);
    }
}
