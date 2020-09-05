<?php

namespace Library\JDDailyBonus;

use Library\Exception\InternalServerErrorException;
use function Common\DI;
use function PhalApi\T;

/**
 * 金融钢镚
 * Class JingRongSteel
 * @package Library\JDDailyBonus
 */
class JingRongSteel
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
        $JRSUrl = [
            'url' => 'https://ms.jr.jd.com/gw/generic/gry/h5/m/signIn',
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Cookie' => $this->initial->KEY,
            ],
            'body' => 'reqData=%7B%22channelSource%22%3A%22JRAPP%22%2C%22riskDeviceParam%22%3A%22%7B%7D%22%7D',
        ];
        $this->initial->custom->post($JRSUrl, function ($error, $response, $data) use ($stop) {
            try {
                if ($error) {
                    throw new InternalServerErrorException(T($error));
                } else {
                    $cc = json_decode($data, true);
                    $Details = $this->initial->LogDetails ? "response:\n" . $data : '';
                    if (preg_match('/\"resBusiCode\":0/', $data)) {
                        $this->initial->custom->log("京东金融-钢镚签到成功 " . $Details);
                        $leng = $cc['resultData']['resBusiData']['actualTotalRewardsValue'];
                        if (strlen($leng) == 1) {
                            $this->initial->merge['JRSteel']['notify'] = "京东金融-钢镚: 成功, 明细: " . "0.0" . $cc['resultData']['resBusiData']['actualTotalRewardsValue'] . "钢镚 💰";
                            $this->initial->merge['JRSteel']['success'] = 1;
                            $this->initial->merge['JRSteel']['steel'] = "0.0" . $cc['resultData']['resBusiData']['actualTotalRewardsValue'];
                        } else {
                            $this->initial->merge['JRSteel']['notify'] = "京东金融-钢镚: 成功, 明细: " . "0." . $cc['resultData']['resBusiData']['actualTotalRewardsValue'] . "钢镚 💰";
                            $this->initial->merge['JRSteel']['success'] = 1;
                            $this->initial->merge['JRSteel']['steel'] = "0." . $cc['resultData']['resBusiData']['actualTotalRewardsValue'];
                        }
                    } else {
                        $this->initial->custom->log("京东金融-钢镚签到失败 " . $Details);
                        if (preg_match('/(已经领取|\"resBusiCode\":15)/', $data)) {
                            $this->initial->merge['JRSteel']['notify'] = "京东金融-钢镚: 失败, 原因: 已签过 ⚠️";
                            $this->initial->merge['JRSteel']['fail'] = 1;
                        } else {
                            if (preg_match('/未实名/', $data)) {
                                $this->initial->merge['JRSteel']['notify'] = "京东金融-钢镚: 失败, 原因: 账号未实名 ⚠️";
                                $this->initial->merge['JRSteel']['fail'] = 1;
                            } else {
                                if (preg_match('/(\"resultCode\":3|请先登录)/', $data)) {
                                    $this->initial->merge['JRSteel']['notify'] = "京东金融-钢镚: 失败, 原因: Cookie失效‼️";
                                    $this->initial->merge['JRSteel']['fail'] = 1;
                                } else {
                                    $this->initial->merge['JRSteel']['notify'] = "京东金融-钢镚: 失败, 原因: 未知 ⚠️";
                                    $this->initial->merge['JRSteel']['fail'] = 1;
                                }
                            }
                        }
                    }
                }
            } catch (\Exception $eor) {
                $this->initial->custom->AnError('京东金融-钢镚', 'JRSteel', $eor);
            } finally {
                return;
            }

        });
    }

}
