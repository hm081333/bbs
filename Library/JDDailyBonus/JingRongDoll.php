<?php

namespace Library\JDDailyBonus;

use Library\Exception\InternalServerErrorException;
use function Common\DI;
use function PhalApi\T;

/**
 * 金融抓娃娃
 * Class JingRongDoll
 * @package Library\JDDailyBonus
 */
class JingRongDoll
{
    /**
     * @var initial
     */
    private $initial; // 初始化参数

    public function __construct(initial $initial)
    {
        $this->initial = $initial;
    }

    public function main($stop = 0, $type = false, $num = false)
    {
        usleep($stop * 1000);
        $DollUrl = [
            'url' => 'https://nu.jr.jd.com/gw/generic/jrm/h5/m/process',
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Cookie' => $this->initial->KEY,
            ],
            'body' => 'reqData=%7B%22actCode%22%3A%22890418F764%22%2C%22type%22%3A' . ($type ? $type : '3') . '%7D',
        ];
        $this->initial->custom->post($DollUrl, function ($error, $response, $data) use ($stop, $num) {
            try {
                if ($error) {
                    throw new InternalServerErrorException(T($error));
                } else {
                    $cc = json_decode($data, true);
                    $Details = $this->initial->LogDetails ? "response:\n" . $data : '';
                    if ($cc['resultCode'] == 0) {
                        if ($cc['resultData']['data']['businessData'] != null) {
                            $this->initial->custom->log("京东金融-娃娃登录成功 " . $Details);
                            if ($cc['resultData']['data']['businessData']['pickStatus'] == 2) {
                                if (preg_match('/\"rewardPrice\":\"?(\d+)/', $data, $matches)) {
                                    $JRDoll_bean = $matches[1];
                                    call_user_func([new JingRongDoll($this->initial), 'main'], $stop, "4", $JRDoll_bean);
                                } else {
                                    $this->initial->merge['JRDoll']['success'] = 1;
                                    $this->initial->merge['JRDoll']['notify'] = "京东金融-娃娃: 成功, 明细: 无京豆 🐶";
                                }
                            } else {
                                $this->initial->custom->log("京东金融-娃娃签到失败 " . $Details);
                                $this->initial->merge['JRDoll']['notify'] = "京东金融-娃娃: 失败, 原因: 已签过 ⚠️";
                                $this->initial->merge['JRDoll']['fail'] = 1;
                            }
                        } else if ($cc['resultData']['data']['businessCode'] == 200) {
                            $this->initial->custom->log("京东金融-娃娃签到成功 " . $Details);
                            $this->initial->merge['JRDoll']['bean'] = $num ? $num : 0;
                            $this->initial->merge['JRDoll']['success'] = $num ? 1 : 0;
                            $this->initial->merge['JRDoll']['notify'] = "京东金融-娃娃: 成功, 明细: " . ($num ? $num . "京豆 🐶" : "无京豆 🐶");
                        } else {
                            $this->initial->custom->log("京东金融-娃娃签到异常 " . $Details);
                            $this->initial->merge['JRDoll']['fail'] = 1;
                            $this->initial->merge['JRDoll']['notify'] = "京东金融-娃娃: 失败, 原因: 领取异常 ⚠️";
                        }
                    } else if ($cc['resultCode'] == 3) {
                        $this->initial->custom->log("京东金融-娃娃签到失败 " . $Details);
                        $this->initial->merge['JRDoll']['notify'] = "京东金融-娃娃: 失败, 原因: Cookie失效‼️";
                        $this->initial->merge['JRDoll']['fail'] = 1;
                    } else {
                        $this->initial->custom->log("京东金融-娃娃判断失败 " . $Details);
                        $this->initial->merge['JRDoll']['notify'] = "京东金融-娃娃: 失败, 原因: 未知 ⚠️";
                        $this->initial->merge['JRDoll']['fail'] = 1;
                    }
                }
            } catch (\Exception $eor) {
                $this->initial->custom->AnError('京东金融-娃娃', 'JRDoll', $eor);
            } finally {
                return;
            }

        });
    }

}
