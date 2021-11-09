<?php
declare (strict_types=1);

namespace app\common\controller;

use app\BaseController;
use library\exception\BadRequestException;
use think\facade\Log;

class Ip extends BaseController
{

    /**
     * 获取IP详细信息
     *
     * @return \think\Response
     */
    public function getInfo(string $ip = '')
    {
        if (empty($ip)) {
            $ip = $this->request->ip();// 获得请求IP
        }
        $old_ip = $this->modelIp->where(['ip' => $ip])->find();
        if (!$old_ip) {
            throw new BadRequestException('获取IP信息失败');
            try {
                $data = curl()->setNoRetry()->setTimeout(1000)->get("https://ip.taobao.com/outGetIpInfo?ip={$ip}", 1000);
                $data = json_decode($data, true);
                if (!$data) {
                    throw new BadRequestException('获取IP信息失败');
                }
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                throw new BadRequestException('获取IP信息失败');
            }
            var_dump($data);
            if ($data['code'] !== 0) {
                $msg = $data['msg'] ?? $data['data'];
                throw new BadRequestException($msg);
            }
            $ip_info = $data['data'];
            $this->modelIp->insert(['ip' => $ip, 'info' => serialize($ip_info), 'add_time' => time()]);
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
