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
    /**
     * @var initial
     */
    private $initial; // 初始化参数

    public function __construct(initial $initial)
    {
        $this->initial = $initial;
    }

    public function main($stop = 0, $id = false)
    {
        usleep($stop * 1000);
        $JDMCUrl = [
            'url' => 'https://api.m.jd.com/client.action?functionId=getNewsInteractionLotteryInfo&appid=smfe' . $id ? "&body=%7B%22interactionId%22%3A{id}%7D" : '',
            'headers' => [
                'Cookie' => $this->initial->KEY,
            ],
        ];
        $this->initial->custom->get($JDMCUrl, function ($error, $response, $data) use ($stop) {
            try {
                if ($error) {
                    throw new InternalServerErrorException(T($error));
                } else {
                    $Details = $this->initial->LogDetails ? "response:\n" . $data : '';
                    $cc = json_decode($data, true);
                    if (preg_match('/(\"name\":)/', $data)) {
                        $this->initial->custom->log("京东商城-魔方签到成功 " . $Details);
                        if (preg_match('/(\"name\":\"京豆\")/', $data)) {
                            $this->initial->merge['JDCube']['notify'] = "京东商城-魔方: 成功, 明细: " . $cc['result']['lotteryInfo']['quantity'] . "京豆 🐶";
                            $this->initial->merge['JDCube']['bean'] = $cc['result']['lotteryInfo']['quantity'];
                            $this->initial->merge['JDCube']['success'] = 1;
                        } else {
                            $this->initial->merge['JDCube']['notify'] = "京东商城-魔方: 成功, 明细: " . $cc['result']['lotteryInfo']['name'] . " 🎉";
                            $this->initial->merge['JDCube']['success'] = 1;
                        }
                    } else {
                        $this->initial->custom->log("京东商城-魔方签到失败 " . $Details);
                        if (preg_match('/(一闪而过|已签到|已领取)/', $data)) {
                            $this->initial->merge['JDCube']['notify'] = "京东商城-魔方: 失败, 原因: 无机会 ⚠️";
                            $this->initial->merge['JDCube']['fail'] = 1;
                        } else {
                            if (preg_match('/(不存在|已结束)/', $data)) {
                                $this->initial->merge['JDCube']['notify'] = "京东商城-魔方: 失败, 原因: 活动已结束 ⚠️";
                                $this->initial->merge['JDCube']['fail'] = 1;
                            } else {
                                if (preg_match('/(\"code\":3)/', $data)) {
                                    $this->initial->merge['JDCube']['notify'] = "京东商城-魔方: 失败, 原因: Cookie失效‼️";
                                    $this->initial->merge['JDCube']['fail'] = 1;
                                } else {
                                    $this->initial->merge['JDCube']['notify'] = "京东商城-魔方: 失败, 原因: 未知 ⚠️";
                                    $this->initial->merge['JDCube']['fail'] = 1;
                                }
                            }
                        }
                    }
                }
            } catch (\Exception $eor) {
                $this->initial->custom->AnError('京东商城-魔方', 'JDCube', $eor);
            } finally {
                return;
            }

        });
    }

}
