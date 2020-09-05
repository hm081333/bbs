<?php

namespace Library\JDDailyBonus;

use Library\Exception\InternalServerErrorException;
use function Common\DI;
use function PhalApi\T;

/**
 * 京东抽大奖 签到
 * Class JDPrizeCheckin
 * @package Library\JDDailyBonus
 */
class JDPrizeCheckin
{
    /**
     * @var initial
     */
    private $initial; // 初始化参数

    public function __construct(initial $initial)
    {
        $this->initial = $initial;
    }

    public function main($stop = 0, $key = false)
    {
        usleep($stop * 1000);
        $JDPUrl = [
            'url' => 'https://api.m.jd.com/client.action?functionId=vvipscdp_raffleAct_lotteryDraw&body=%7B%22raffleActKey%22%3A%22' . $key . '%22%2C%22drawType%22%3A0%2C%22riskInformation%22%3A%7B%7D%7D&client=apple&clientVersion=8.1.0&appid=member_benefit_m',
            'headers' => [
                'Cookie' => $this->initial->KEY,
                'Referer' => 'https://jdmall.m.jd.com/beansForPrizes',
            ],
        ];
        $this->initial->custom->get($JDPUrl, function ($error, $response, $data) use ($stop) {
            try {
                if ($error) {
                    throw new InternalServerErrorException(T($error));
                } else {
                    $Details = $this->initial->LogDetails ? "response:\n" . $data : '';
                    $c = json_decode($data, true);
                    if (preg_match('/\"success\":true/', $data)) {
                        $this->initial->custom->log("京东商城-大奖签到成功 " . $Details);
                        if (preg_match('/\"beanNumber\":\d+/', $data)) {
                            $this->initial->merge['JDPrize']['notify'] = "京东商城-大奖: 成功, 明细: " . $c['data']['beanNumber'] . "京豆 🐶";
                            $this->initial->merge['JDPrize']['success'] = 1;
                            $this->initial->merge['JDPrize']['bean'] = $c['data']['beanNumber'];
                        } else if (preg_match('/\"couponInfoVo\"/', $data)) {
                            if (preg_match('/\"limitStr\"/', $data)) {
                                $this->initial->merge['JDPrize']['notify'] = "京东商城-大奖: 获得满" . $c['data']['couponInfoVo']['quota'] . "减" . $c['data']['couponInfoVo']['discount'] . "优惠券→ " . $c['data']['couponInfoVo']['limitStr'];
                                $this->initial->merge['JDPrize']['success'] = 1;
                            } else {
                                $this->initial->merge['JDPrize']['notify'] = "京东商城-大奖: 成功, 明细: 优惠券";
                                $this->initial->merge['JDPrize']['success'] = 1;
                            }
                        } else if (preg_match('/\"pitType\":0/', $data)) {
                            $this->initial->merge['JDPrize']['notify'] = "京东商城-大奖: 成功, 明细: 未中奖 🐶";
                            $this->initial->merge['JDPrize']['success'] = 1;
                        } else {
                            $this->initial->merge['JDPrize']['notify'] = "京东商城-大奖: 成功, 明细: 未知 🐶";
                            $this->initial->merge['JDPrize']['success'] = 1;
                        }
                    } else {
                        $this->initial->custom->log("京东商城-大奖签到失败 " . $Details);
                        $this->initial->merge['JDPrize']['fail'] = 1;
                        if (preg_match('/(已用光|7000003)/', $data)) {
                            $this->initial->merge['JDPrize']['notify'] = "京东商城-大奖: 失败, 原因: 已签过 ⚠️";
                        } else if (preg_match('/(未登录|\"101\")/', $data)) {
                            $this->initial->merge['JDPrize']['notify'] = "京东商城-大奖: 失败, 原因: Cookie失效‼️";
                        } else if (preg_match('/7000005/', $data)) {
                            $this->initial->merge['JDPrize']['notify'] = "京东商城-大奖: 失败, 原因: 未中奖 ⚠️";
                        } else {
                            $this->initial->merge['JDPrize']['notify'] = "京东商城-大奖: 失败, 原因: 未知 ⚠️";
                        }
                    }
                }
            } catch (\Exception $eor) {
                $this->initial->custom->AnError('京东大奖-签到', 'JDPrize', $eor);
            } finally {
                return;
            }

        });
    }

}
