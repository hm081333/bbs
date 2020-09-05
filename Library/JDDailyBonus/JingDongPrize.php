<?php

namespace Library\JDDailyBonus;

use Library\Exception\InternalServerErrorException;
use function Common\DI;
use function PhalApi\T;

/**
 * 京东抽大奖
 * Class JingDongPrize
 * @package Library\JDDailyBonus
 */
class JingDongPrize
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
        $JDkey = [
            'url' => 'https://api.m.jd.com/client.action?functionId=vvipscdp_raffleAct_index&client=apple&clientVersion=8.1.0&appid=member_benefit_m',
            'headers' => [
                'Cookie' => $this->initial->KEY,
                'Referer' => 'https://jdmall.m.jd.com/beansForPrizes',
            ],
        ];
        $this->initial->custom->get($JDkey, function ($error, $response, $data) use ($stop) {
            try {
                if ($error) {
                    throw new InternalServerErrorException(T($error));
                } else {
                    $Details = $this->initial->LogDetails ? "response:\n" . $data : '';
                    if (preg_match('/\"raffleActKey\":\"[a-zA-z0-9]{3,}\"/', $data)) {
                        $cc = json_decode($data, true);
                        $this->initial->merge['JDPrize']['key'] = $cc['data']['floorInfoList'][0]['detail']['raffleActKey'];
                        $this->initial->custom->log("京东商城-大奖查询成功 " . $Details);
                        if ($this->initial->merge['JDPrize']['key']) {
                            call_user_func([new JDPrizeCheckin($this->initial), 'main'], $stop, $this->initial->merge['JDPrize']['key']);
                        } else {
                            $this->initial->merge['JDPrize']['notify'] = "京东商城-大奖: 失败, 原因: 无奖池 ⚠️";
                            $this->initial->merge['JDPrize']['fail'] = 1;
                        }
                    } else {
                        $this->initial->custom->log("京东商城-大奖查询KEY失败 " . $Details);
                        if (preg_match('/(未登录|\"101\")/', $data)) {
                            $this->initial->merge['JDPrize']['notify'] = "京东大奖-登录: 失败, 原因: Cookie失效‼️";
                            $this->initial->merge['JDPrize']['fail'] = 1;
                        } else {
                            $this->initial->merge['JDPrize']['notify'] = "京东大奖-登录: 失败, 原因: 未知 ⚠️";
                            $this->initial->merge['JDPrize']['fail'] = 1;
                        }
                    }
                }
            } catch (\Exception $eor) {
                $this->initial->custom->AnError('京东大奖-查询', 'JDPrize', $eor);
            } finally {
                return;
            }

        });
    }

}
