<?php

namespace Library\JDDailyBonus;

use Library\Exception\InternalServerErrorException;
use function Common\DI;
use function PhalApi\T;

/**
 * 京东小魔方 签到
 * Class JDMagicCubeSign
 * @package Library\JDDailyBonus
 */
class JDMagicCubeSign
{
    private $JDMCUrl;
    private $KEY;
    private $LogDetails = false; //是否开启响应日志, true则开启

    public function __construct()
    {
    }

    public function main($stop = 0, $id = false)
    {
        usleep($stop * 1000);
        $this->JDMCUrl = [
            'url' => 'https://api.m.jd.com/client.action?functionId=getNewsInteractionLotteryInfo&appid=smfe' . $id ? "&body=%7B%22interactionId%22%3A{id}%7D" : '',
            'headers' => [
                'Cookie' => $this->KEY,
            ],
        ];
        $nobyda = new nobyda();
        $nobyda->get($this->JDMCUrl, function ($error, $response, $data) use ($nobyda, $stop) {
            try {
                if ($error) {
                    throw new InternalServerErrorException(T($error));
                } else {
                    $Details = $this->LogDetails ? "response:\n" . $data : '';
                    $cc = json_decode($data, true);
                    if (preg_match('/(\"name\":)/', $data)) {
                        DI()->logger->info("京东商城-魔方签到成功 " . $Details);
                        if (preg_match('/(\"name\":\"京豆\")/', $data)) {
                            $merge['JDCube']['notify'] = "京东商城-魔方: 成功, 明细: " . $cc['result']['lotteryInfo']['quantity'] . "京豆 🐶";
                            $merge['JDCube']['bean'] = $cc['result']['lotteryInfo']['quantity'];
                            $merge['JDCube']['success'] = 1;
                        } else {
                            $merge['JDCube']['notify'] = "京东商城-魔方: 成功, 明细: " . $cc['result']['lotteryInfo']['name'] . " 🎉";
                            $merge['JDCube']['success'] = 1;
                        }
                    } else {
                        DI()->logger->info("京东商城-魔方签到失败 " . $Details);
                        if (preg_match('/(一闪而过|已签到|已领取)/', $data)) {
                            $merge['JDCube']['notify'] = "京东商城-魔方: 失败, 原因: 无机会 ⚠️";
                            $merge['JDCube']['fail'] = 1;
                        } else {
                            if (preg_match('/(不存在|已结束)/', $data)) {
                                $merge['JDCube']['notify'] = "京东商城-魔方: 失败, 原因: 活动已结束 ⚠️";
                                $merge['JDCube']['fail'] = 1;
                            } else {
                                if (preg_match('/(\"code\":3)/', $data)) {
                                    $merge['JDCube']['notify'] = "京东商城-魔方: 失败, 原因: Cookie失效‼️";
                                    $merge['JDCube']['fail'] = 1;
                                } else {
                                    $merge['JDCube']['notify'] = "京东商城-魔方: 失败, 原因: 未知 ⚠️";
                                    $merge['JDCube']['fail'] = 1;
                                }
                            }
                        }
                    }
                }
            } catch (\Exception $eor) {
                $nobyda->AnError('京东商城-魔方', 'JDCube', $eor);
            } finally {
                return;
            }

        });
    }

}
