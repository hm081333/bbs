<?php
/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2018-11-26
 * Time: 11:14:57
 */

namespace Common\Domain;


use Library\Traits\Domain;

/**
 * 物流公司 领域层
 * Class Logistics
 * @package Common\Domain
 * @author  LYi-Ho 2018-11-26 11:14:57
 */
class Logistics
{
    use Domain;

    /**
     * 请求快递100 获取物流信息
     * @param string $code 物流公司代号
     * @param string $sn   物流编号
     * @return array
     */
    public static function queryLogistics(string $code, string $sn)
    {
        // $result = self::DI()->curl->get('http://www.kuaidi100.com/query?type=' . $code . '&postid=' . $sn);
        // return json_decode($result, true);
        return self::DI()->curl->setCookie([
            'Hm_lpvt_22ea01af58ba2be0fec7c11b25e88e6c' => time(),
        ])->setHeader([
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Referer' => 'http://baidu.kuaidi100.com/',
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.45 Safari/537.36',
        ])->json_get("http://baidu.kuaidi100.com/query?type={$code}&postid={$sn}&id=4&temp=0." . rand(100000000000000, 999999999999999));
    }
}
