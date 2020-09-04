<?php

namespace Library\JDDailyBonus;

use Library\Exception\InternalServerErrorException;
use function Common\DI;
use function PhalApi\T;

/**
 * 京东摇一摇
 * Class JingDongShake
 * @package Library\JDDailyBonus
 */
class JingDongShake
{
    private $JDSh;
    private $KEY;
    private $LogDetails = false; //是否开启响应日志, true则开启

    public function __construct()
    {
    }

    public function main($stop = 0)
    {
        usleep($stop * 1000);
        $this->JDSh = [
            'url' => 'https://api.m.jd.com/client.action?appid=vip_h5&functionId=vvipclub_shaking',
            'headers' => [
                'Cookie' => $this->KEY,
            ],
        ];
        $nobyda = new nobyda();
        $nobyda->get($this->JDSh, function ($error, $response, $data) use ($nobyda, $stop) {
            try {
                if ($error) {
                    throw new InternalServerErrorException(T($error));
                } else {
                    $Details = $this->LogDetails ? "response:\n" . $data : '';
                    $cc = json_decode($data, true);
                    if (preg_match('/prize/', $data)) {
                        DI()->logger->info("京东商城-摇一摇签到成功 " . $Details);
                        if ($cc['data']['prizeBean']) {
                            $merge['JDShake']['notify'] .= $merge['JDShake']['notify'] ? "\n京东商城-摇摇: 成功, 明细: " . $cc['data']['prizeBean']['count'] . "京豆 🐶 (多次)" : "京东商城-摇摇: 成功, 明细: " . $cc['data']['prizeBean']['count'] . "京豆 🐶";
                            $merge['JDShake']['bean'] += $cc['data']['prizeBean']['count'];
                            $merge['JDShake']['success'] += 1;
                        } else {
                            if ($cc['data']['prizeCoupon']) {
                                $merge['JDShake']['notify'] .= $merge['JDShake']['notify'] ? "\n京东商城-摇摇(多次): 获得满" . $cc['data']['prizeCoupon']['quota'] . "减" . $cc['data']['prizeCoupon']['discount'] . "优惠券→ " . $cc['data']['prizeCoupon']['limitStr'] : "京东商城-摇摇: 获得满" . $cc['data']['prizeCoupon']['quota'] . "减" . $cc['data']['prizeCoupon']['discount'] . "优惠券→ " . $cc['data']['prizeCoupon']['limitStr'];
                                $merge['JDShake']['success'] += 1;
                            } else {
                                $merge['JDShake']['notify'] .= $merge['JDShake']['notify'] ? "\n京东商城-摇摇: 失败, 原因: 未知 ⚠️ (多次)" : "京东商城-摇摇: 失败, 原因: 未知 ⚠️";
                                $merge['JDShake']['fail'] += 1;
                            }
                        }
                        if ($cc['data']['luckyBox']['freeTimes'] != 0) {
                            call_user_func([new JingDongShake,'main'],$stop);
                        }
                    } else {
                        DI()->logger->info("京东商城-摇一摇签到失败 " . $Details);
                        if (preg_match('/true/', $data)) {
                            $merge['JDShake']['notify'] .= $merge['JDShake']['notify'] ? "\n京东商城-摇摇: 成功, 明细: 无奖励 🐶 (多次)" : "京东商城-摇摇: 成功, 明细: 无奖励 🐶";
                            $merge['JDShake']['success'] += 1;
                            if ($cc['data']['luckyBox']['freeTimes'] != 0) {
                                call_user_func([new JingDongShake,'main'],$stop);
                            }
                        } else {
                            if (preg_match('/(无免费|8000005|9000005)/', $data)) {
                                $merge['JDShake']['notify'] = "京东商城-摇摇: 失败, 原因: 已摇过 ⚠️";
                                $merge['JDShake']['fail'] = 1;
                            } else if (preg_match('/(未登录|101)/', $data)) {
                                $merge['JDShake']['notify'] = "京东商城-摇摇: 失败, 原因: Cookie失效‼️";
                                $merge['JDShake']['fail'] = 1;
                            } else {
                                $merge['JDShake']['notify'] .= $merge['JDShake']['notify'] ? "\n京东商城-摇摇: 失败, 原因: 未知 ⚠️ (多次)" : "京东商城-摇摇: 失败, 原因: 未知 ⚠️";
                                $merge['JDShake']['fail'] += 1;
                            }
                        }
                    }
                }
            } catch (\Exception $eor) {
                $nobyda->AnError('京东商城-摇摇', 'JDShake', $eor);
            } finally {
                return;
            }

        });
    }

}
