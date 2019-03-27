<?php
/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2018-11-26
 * Time: 11:14:57
 */

namespace Common\Domain;

/**
 * IP地址 领域层
 * Class Ip
 * @package Common\Domain
 * @author  LYi-Ho 2018-11-26 11:14:57
 */
class Ip
{
    use Common;

    /**
     * 获取ip地址的详细信息
     * @param array $data
     * @return mixed
     * @throws \Exception\BadRequestException
     * @throws \Exception\InternalServerErrorException
     */
    public static function getIPInfo(array $data = [])
    {
        $ip = $data['ip'] ?? '';
        if (empty($ip)) {
            $ip = \PhalApi\DI()->tool->getClientIp();// 获得请求IP
        }
        $ip_model = self::getModel();
        $old_ip = $ip_model->getInfo(['ip' => $ip]);
        if (!$old_ip) {
            $data = \PhalApi\DI()->curl->get("http://ip.taobao.com/service/getIpInfo.php?ip={$ip}");
            $data = json_decode($data, TRUE);
            if (!$data) {
                throw new \Exception\BadRequestException(\PhalApi\T('获取IP失败'));
            }
            if ($data['code'] !== 0) {
                throw new \Exception\InternalServerErrorException(\PhalApi\T($data['data']));
            }
            $ip_info = $data['data'];
            $ip_model->insert(['ip' => $ip, 'info' => serialize($ip_info), 'add_time' => NOW_TIME]);
        } else {
            $ip_info = unserialize($old_ip['info']);
        }
        array_walk($ip_info, function (&$value, $key) {
            if ((strpos('country,area,region,city,isp', $key)) !== FALSE) {
                $value = \PhalApi\T($value);
            }
        });
        return $ip_info;
    }

}
