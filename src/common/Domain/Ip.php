<?php
/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2018-11-26
 * Time: 11:14:57
 */

namespace Common\Domain;

use function Common\DI;
use Common\Model\Test;

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
     * @throws \Library\Exception\BadRequestException
     * @throws \Library\Exception\InternalServerErrorException
     */
    public static function getIPInfo(array $data = [])
    {
        $ip = $data['ip'] ?? '';
        if (empty($ip)) {
            $ip = self::DI()->tool->getClientIp();// 获得请求IP
        }
        $ip_model = self::getModel();
        $old_ip = $ip_model->getInfo(['ip' => $ip]);
        if (!$old_ip) {
            $data = DI()->curl->get("http://ip.taobao.com/service/getIpInfo.php?ip={$ip}");
            $data = json_decode($data, true);
            if (!$data) {
                throw new \Library\Exception\BadRequestException(\PhalApi\T('获取IP信息失败'));
            }
            if ($data['code'] !== 0) {
                throw new \Library\Exception\InternalServerErrorException(\PhalApi\T($data['data']));
            }
            $ip_info = $data['data'];
            $ip_model->insert(['ip' => $ip, 'info' => serialize($ip_info), 'add_time' => NOW_TIME]);
        } else {
            $ip_info = unserialize($old_ip['info']);
        }
        array_walk($ip_info, function (&$value, $key) {
            if ((strpos('country,area,region,city,isp', $key)) !== false) {
                $value = \PhalApi\T($value);
            }
        });
        return $ip_info;
    }

}
