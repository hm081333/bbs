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
    private $JDPUrl;
    private $KEY;
    private $LogDetails = false; //是否开启响应日志, true则开启

    public function __construct()
    {
    }

    public function main($stop = 0, $key = false)
    {
        usleep($stop * 1000);
        $this->JDPUrl = [
            'url' => 'https://api.m.jd.com/client.action?functionId=vvipscdp_raffleAct_lotteryDraw&body=%7B%22raffleActKey%22%3A%22' . $key . '%22%2C%22drawType%22%3A0%2C%22riskInformation%22%3A%7B%7D%7D&client=apple&clientVersion=8.1.0&appid=member_benefit_m',
            'headers' => [
                'Cookie' => $this->KEY,
                'Referer' => 'https://jdmall.m.jd.com/beansForPrizes',
            ],
        ];
        $nobyda = new nobyda();
        $nobyda->get($this->JDPUrl, function ($error, $response, $data) use ($nobyda, $stop) {
            try {
                if ($error) {
                    throw new InternalServerErrorException(T($error));
                } else {
                    $Details = $this->LogDetails ? "response:\n" . $data : '';
                    $c = json_decode($data, true);
                    if (preg_match('/\"success\":true/', $data)) {
                        DI()->logger->info("京东商城-大奖签到成功 " . $Details);
                        if (preg_match('/\"beanNumber\":\d+/', $data)) {
                            $merge['JDPrize']['notify'] = "京东商城-大奖: 成功, 明细: " . $c['data']['beanNumber'] . "京豆 🐶";
                            $merge['JDPrize']['success'] = 1;
                            $merge['JDPrize']['bean'] = $c['data']['beanNumber'];
                        } else if (preg_match('/\"couponInfoVo\"/', $data)) {
                            if (preg_match('/\"limitStr\"/', $data)) {
                                $merge['JDPrize']['notify'] = "京东商城-大奖: 获得满" . $c['data']['couponInfoVo']['quota'] . "减" . $c['data']['couponInfoVo']['discount'] . "优惠券→ " . $c['data']['couponInfoVo']['limitStr'];
                                $merge['JDPrize']['success'] = 1;
                            } else {
                                $merge['JDPrize']['notify'] = "京东商城-大奖: 成功, 明细: 优惠券";
                                $merge['JDPrize']['success'] = 1;
                            }
                        } else if (preg_match('/\"pitType\":0/', $data)) {
                            $merge['JDPrize']['notify'] = "京东商城-大奖: 成功, 明细: 未中奖 🐶";
                            $merge['JDPrize']['success'] = 1;
                        } else {
                            $merge['JDPrize']['notify'] = "京东商城-大奖: 成功, 明细: 未知 🐶";
                            $merge['JDPrize']['success'] = 1;
                        }
                    } else {
                        DI()->logger->info("京东商城-大奖签到失败 " . $Details);
                        $merge['JDPrize']['fail'] = 1;
                        if (preg_match('/(已用光|7000003)/', $data)) {
                            $merge['JDPrize']['notify'] = "京东商城-大奖: 失败, 原因: 已签过 ⚠️";
                        } else if (preg_match('/(未登录|\"101\")/', $data)) {
                            $merge['JDPrize']['notify'] = "京东商城-大奖: 失败, 原因: Cookie失效‼️";
                        } else if (preg_match('/7000005/', $data)) {
                            $merge['JDPrize']['notify'] = "京东商城-大奖: 失败, 原因: 未中奖 ⚠️";
                        } else {
                            $merge['JDPrize']['notify'] = "京东商城-大奖: 失败, 原因: 未知 ⚠️";
                        }
                    }
                }
            } catch (\Exception $eor) {
                $nobyda->AnError('京东大奖-签到', 'JDPrize', $eor);
            } finally {
                return;
            }

        });
    }

}
