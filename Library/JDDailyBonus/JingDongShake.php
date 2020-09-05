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
     /**
     * @var initial
     */
    private $initial; // 初始化参数

    public function __construct(initial $initial)
    {
        $this->initial = $initial;
    }

    public function main($stop = 0)
    {
        usleep($stop * 1000);
        $JDSh = [
            'url' => 'https://api.m.jd.com/client.action?appid=vip_h5&functionId=vvipclub_shaking',
            'headers' => [
                'Cookie' => $this->initial->KEY,
            ],
        ];
        $this->initial->custom->get($JDSh, function ($error, $response, $data) use ( $stop) {
            try {
                if ($error) {
                    throw new InternalServerErrorException(T($error));
                } else {
                    $Details = $this->initial->LogDetails ? "response:\n" . $data : '';
                    $cc = json_decode($data, true);
                    if (preg_match('/prize/', $data)) {
                        $this->initial->custom->log("京东商城-摇一摇签到成功 " . $Details);
                        if ($cc['data']['prizeBean']) {
                            $this->initial->merge['JDShake']['notify'] .= $this->initial->merge['JDShake']['notify'] ? "\n京东商城-摇摇: 成功, 明细: " . $cc['data']['prizeBean']['count'] . "京豆 🐶 (多次)" : "京东商城-摇摇: 成功, 明细: " . $cc['data']['prizeBean']['count'] . "京豆 🐶";
                            $this->initial->merge['JDShake']['bean'] += $cc['data']['prizeBean']['count'];
                            $this->initial->merge['JDShake']['success'] += 1;
                        } else {
                            if ($cc['data']['prizeCoupon']) {
                                $this->initial->merge['JDShake']['notify'] .= $this->initial->merge['JDShake']['notify'] ? "\n京东商城-摇摇(多次): 获得满" . $cc['data']['prizeCoupon']['quota'] . "减" . $cc['data']['prizeCoupon']['discount'] . "优惠券→ " . $cc['data']['prizeCoupon']['limitStr'] : "京东商城-摇摇: 获得满" . $cc['data']['prizeCoupon']['quota'] . "减" . $cc['data']['prizeCoupon']['discount'] . "优惠券→ " . $cc['data']['prizeCoupon']['limitStr'];
                                $this->initial->merge['JDShake']['success'] += 1;
                            } else {
                                $this->initial->merge['JDShake']['notify'] .= $this->initial->merge['JDShake']['notify'] ? "\n京东商城-摇摇: 失败, 原因: 未知 ⚠️ (多次)" : "京东商城-摇摇: 失败, 原因: 未知 ⚠️";
                                $this->initial->merge['JDShake']['fail'] += 1;
                            }
                        }
                        if ($cc['data']['luckyBox']['freeTimes'] != 0) {
                            call_user_func([new JingDongShake($this->initial),'main'],$stop);
                        }
                    } else {
                        $this->initial->custom->log("京东商城-摇一摇签到失败 " . $Details);
                        if (preg_match('/true/', $data)) {
                            $this->initial->merge['JDShake']['notify'] .= $this->initial->merge['JDShake']['notify'] ? "\n京东商城-摇摇: 成功, 明细: 无奖励 🐶 (多次)" : "京东商城-摇摇: 成功, 明细: 无奖励 🐶";
                            $this->initial->merge['JDShake']['success'] += 1;
                            if ($cc['data']['luckyBox']['freeTimes'] != 0) {
                                call_user_func([new JingDongShake($this->initial),'main'],$stop);
                            }
                        } else {
                            if (preg_match('/(无免费|8000005|9000005)/', $data)) {
                                $this->initial->merge['JDShake']['notify'] = "京东商城-摇摇: 失败, 原因: 已摇过 ⚠️";
                                $this->initial->merge['JDShake']['fail'] = 1;
                            } else if (preg_match('/(未登录|101)/', $data)) {
                                $this->initial->merge['JDShake']['notify'] = "京东商城-摇摇: 失败, 原因: Cookie失效‼️";
                                $this->initial->merge['JDShake']['fail'] = 1;
                            } else {
                                $this->initial->merge['JDShake']['notify'] .= $this->initial->merge['JDShake']['notify'] ? "\n京东商城-摇摇: 失败, 原因: 未知 ⚠️ (多次)" : "京东商城-摇摇: 失败, 原因: 未知 ⚠️";
                                $this->initial->merge['JDShake']['fail'] += 1;
                            }
                        }
                    }
                }
            } catch (\Exception $eor) {
                $this->initial->custom->AnError('京东商城-摇摇', 'JDShake', $eor);
            } finally {
                return;
            }

        });
    }

}
