<?php

namespace Library\JDDailyBonus;

use Library\Exception\InternalServerErrorException;
use function Common\DI;
use function PhalApi\T;

/**
 * 金融京豆
 * Class JingRongBean
 * @package Library\JDDailyBonus
 */
class JingRongBean
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
        $login = [
            'url' => 'https://ms.jr.jd.com/gw/generic/zc/h5/m/queryOpenScreenReward',
            'headers' => [
                // 'Content-Type' => 'application/x-www-form-urlencoded',
                'Cookie' => $this->initial->KEY,
                // 'Referer' => 'https://jddx.jd.com/m/money/index.html?from=sign',
            ],
            'body' => 'reqData=%7B%22channelCode%22%3A%22ZHUANQIAN%22%2C%22clientType%22%3A%22sms%22%2C%22clientVersion%22%3A%2211.0%22%7D',
        ];
        $this->initial->custom->post($login, function ($error, $response, $data) use ($stop) {
            try {
                if ($error) {
                    throw new InternalServerErrorException(T($error));
                } else {
                    $Details = $this->initial->LogDetails ? "response:\n" . $data : '';
                    if (preg_match('/\"login\":true/', $data)) {
                        $this->initial->custom->log("京东金融-金贴查询成功 " . $Details);
                        $cc = json_decode($data, true);
                        if ($cc['resultData']['data']['reward'] === false) {
                            call_user_func([new JRBeanCheckin($this->initial), 'main'], $stop, $cc['resultData']['data']['rewardAmount']);
                        } else {
                            $tp = $cc['resultData']['data']['reward'] === true;
                            $this->initial->merge->JRBean->notify = "京东金融-金贴: 失败, 原因: " . ($tp ? "已签过" : "未知") . " ⚠️";
                            $this->initial->merge->JRBean->fail = 1;
                        }
                    } else {
                        $this->initial->custom->log("京东金融-金贴查询失败 " . $Details);
                        $lt = preg_match('/\"login\":false/', $data);
                        $this->initial->merge->JRBean->fail = 1;
                        $this->initial->merge->JRBean->notify = "京东金融-金贴: 失败, 原因: " . ($lt ? "Cookie失效‼️" : "未知 ⚠️");
                    }
                }
            } catch (\Exception $eor) {
                $this->initial->custom->AnError('金融金贴-查询', 'JRBean', $eor);
            } finally {
                return;
            }

        });
    }

}
