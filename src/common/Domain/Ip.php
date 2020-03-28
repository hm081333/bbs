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
     * IP库 数据层
     * @return \Common\Model\Ip
     * @throws BadRequestException
     */
    protected function Model_Ip()
    {
        return self::getModel();
    }

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
        $old_ip = $this->Model_Ip()->getInfo(['ip' => $ip]);
        if (!$old_ip) {
            try {
                $data = DI()->curl->setNoRetry()->setTimeout(1000)->get("http://ip.taobao.com/service/getIpInfo.php?ip={$ip}", 1000);
                $data = json_decode($data, true);
                if (!$data) {
                    throw new BadRequestException(T('获取IP信息失败'));
                }
            } catch (\Exception $e) {
                DI()->logger->error($e->getMessage());
                throw new BadRequestException(T('获取IP信息失败'));
            }
            if ($data['code'] !== 0) {
                throw new InternalServerErrorException(T($data['data']));
            }
            $ip_info = $data['data'];
            $this->Model_Ip()->insert(['ip' => $ip, 'info' => serialize($ip_info), 'add_time' => time()]);
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
