<?php
/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2018-11-26
 * Time: 11:14:57
 */

namespace Common\Domain;

use Library\Exception\BadRequestException;
use Library\Exception\InternalServerErrorException;
use Library\Traits\Domain;
use function Common\DI;
use function PhalApi\T;

/**
 * IP地址 领域层
 * Class Ip
 * @package Common\Domain
 * @author  LYi-Ho 2018-11-26 11:14:57
 */
class Ip
{
    use Domain;

    /**
     * 获取ip地址的详细信息
     * @param string $ip
     * @return mixed
     * @throws BadRequestException
     * @throws InternalServerErrorException
     */
    public function getIPInfo(string $ip)
    {
        if (empty($ip)) {
            $ip = DI()->tool->getClientIp();// 获得请求IP
        }
        $ip_model = self::getModel();
        $old_ip = $ip_model->getInfo(['ip' => $ip]);
        if (!$old_ip) {
            $data = DI()->curl->get("http://ip.taobao.com/service/getIpInfo.php?ip={$ip}");
            $data = json_decode($data, true);
            if (!$data) {
                throw new BadRequestException(T('获取IP信息失败'));
            }
            if ($data['code'] !== 0) {
                throw new InternalServerErrorException(T($data['data']));
            }
            $ip_info = $data['data'];
            $ip_model->insert(['ip' => $ip, 'info' => serialize($ip_info), 'add_time' => time()]);
        } else {
            $ip_info = unserialize($old_ip['info']);
        }
        array_walk($ip_info, function (&$value, $key) {
            if ((strpos('country,area,region,city,isp', $key)) !== false) {
                $value = T($value);
            }
        });
        return $ip_info;
    }

}
