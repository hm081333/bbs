<?php

namespace Library\JDDailyBonus;

use Library\Exception\InternalServerErrorException;
use function Common\DI;
use function PhalApi\T;

/**
 * 金融双签
 * Class JRDoubleSign
 * @package Library\JDDailyBonus
 */
class JRDoubleSign
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
        $JRDSUrl = [
            'url' => 'https://nu.jr.jd.com/gw/generic/jrm/h5/m/process?',
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Cookie' => $this->initial->KEY,
            ],
            'body' => 'reqData=%7B%22actCode%22%3A%22FBBFEC496C%22%2C%22type%22%3A3%2C%22riskDeviceParam%22%3A%22%22%7D',
        ];
        $this->initial->custom->post($JRDSUrl, function ($error, $response, $data) use ($stop) {
            try {
                if ($error) {
                    throw new InternalServerErrorException(T($error));
                } else {
                    $Details = $this->initial->LogDetails ? "response:\n" . $data : '';
                    if (preg_match('/\"resultCode\":0/', $data)) {
                        // if (preg_match('/\"count\":\d+/',$data)) {
                        if (preg_match('/\"count\":(\d+)/', $data, $matches)) {
                            $this->initial->custom->log("京东金融-双签签到成功 " . $Details);
                            $this->initial->merge->JRDSign->bean = $matches[1];
                            $this->initial->merge->JRDSign->notify = "京东金融-双签: 成功, 明细: " . $this->initial->merge->JRDSign->bean . "京豆 🐶";
                            $this->initial->merge->JRDSign->success = 1;
                        } else {
                            $this->initial->custom->log("京东金融-双签签到失败 " . $Details);
                            $this->initial->merge->JRDSign->fail = 1;
                            if (preg_match('/已领取/', $data)) {
                                $this->initial->merge->JRDSign->notify = "京东金融-双签: 失败, 原因: 已签过 ⚠️";
                            } else if (preg_match('/未在/', $data)) {
                                $this->initial->merge->JRDSign->notify = "京东金融-双签: 失败, 原因: 未在京东签到 ⚠️";
                            } else {
                                $this->initial->merge->JRDSign->notify = "京东金融-双签: 失败, 原因: 无奖励 🐶";
                            }
                        }
                    } else {
                        $this->initial->custom->log("京东金融-双签签到失败 " . $Details);
                        $this->initial->merge->JRDSign->fail = 1;
                        if (preg_match('/(\"resultCode\":3|请先登录)/', $data)) {
                            $this->initial->merge->JRDSign->notify = "京东金融-双签: 失败, 原因: Cookie失效‼️";
                        } else {
                            $this->initial->merge->JRDSign->notify = "京东金融-双签: 失败, 原因: 未知 ⚠️";
                        }
                    }
                }
            } catch (\Exception $eor) {
                $this->initial->custom->AnError('京东金融-双签', 'JRDSign', $eor);
            } finally {
                return;
            }

        });
    }

}
