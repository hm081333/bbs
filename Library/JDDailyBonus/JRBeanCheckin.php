<?php

namespace Library\JDDailyBonus;

use Library\Exception\InternalServerErrorException;
use function Common\DI;
use function PhalApi\T;

/**
 * 金融京豆 签到
 * Class JRBeanCheckin
 * @package Library\JDDailyBonus
 */
class JRBeanCheckin
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
        $JRBUrl = [
            'url' => 'https://ms.jr.jd.com/gw/generic/zc/h5/m/signRewardGift',
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Cookie' => $this->initial->KEY,
                'Referer' => 'https://jddx.jd.com/m/jddnew/money/index.html',
            ],
            'body' => 'reqData=%7B%22bizLine%22%3A2%2C%22signDate%22%3A%221%22%2C%22deviceInfo%22%3A%7B%22os%22%3A%22iOS%22%7D%2C%22clientType%22%3A%22sms%22%2C%22clientVersion%22%3A%2211.0%22%7D',
        ];
        $this->initial->custom->post($JRBUrl, function ($error, $response, $data) use ($stop) {
            try {
                if ($error) {
                    throw new InternalServerErrorException(T($error));
                } else {
                    $c = json_decode($data, true);
                    $Details = $this->initial->LogDetails ? "response:\n" . $data : '';
                    if (preg_match('/\"resultCode\":\"00000\"/', $data)) {
                        $this->initial->custom->log("京东金融-金贴签到成功 " . $Details);
                        if ($c['resultData']['data']['rewardAmount'] != "0") {
                            $this->initial->merge['JRBean']['notify'] = "京东金融-金贴: 成功, 明细: " . $c['resultData']['data']['rewardAmount'] . "金贴 💰";
                            $this->initial->merge['JRBean']['success'] = 1;
                            //$this->initial->merge['JRBean']['bean'] = $c['resultData']['data']['rewardAmount'];
                        } else {
                            $this->initial->merge['JRBean']['notify'] = "京东金融-金贴: 成功, 明细: 无奖励 🐶";
                            $this->initial->merge['JRBean']['success'] = 1;
                        }
                    } else {
                        $this->initial->custom->log("京东金融-金贴签到失败 " . $Details);
                        if (preg_match('/发放失败|70111|10000/', $data)) {
                            $this->initial->merge['JRBean']['notify'] = "京东金融-金贴: 失败, 原因: 已签过 ⚠️";
                            $this->initial->merge['JRBean']['fail'] = 1;
                        } else {
                            if (preg_match('/(\"resultCode\":3|请先登录)/', $data)) {
                                $this->initial->merge['JRBean']['notify'] = "京东金融-金贴: 失败, 原因: Cookie失效‼️";
                                $this->initial->merge['JRBean']['fail'] = 1;
                            } else {
                                $this->initial->merge['JRBean']['notify'] = "京东金融-金贴: 失败, 原因: 未知 ⚠️";
                                $this->initial->merge['JRBean']['fail'] = 1;
                            }
                        }
                    }
                }
            } catch (\Exception $eor) {
                $this->initial->custom->AnError('京东金融-金贴', 'JRBean', $eor);
            } finally {
                return;
            }

        });
    }

}
