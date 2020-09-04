<?php

namespace Library\JDDailyBonus;

use Library\Exception\InternalServerErrorException;
use function Common\DI;
use function PhalApi\T;

/**
 * 京东领现金
 * Class JingDongGetCash
 * @package Library\JDDailyBonus
 */
class JingDongGetCash
{
    private $GetCashUrl;
    private $KEY;
    private $LogDetails = false; //是否开启响应日志, true则开启

    public function __construct()
    {
    }

    public function main($stop = 0)
    {
        usleep($stop * 1000);
        $this->GetCashUrl = [
            'url' => 'https://api.m.jd.com/client.action?functionId=cash_sign&body=%7B%22remind%22%3A0%2C%22inviteCode%22%3A%22%22%2C%22type%22%3A0%2C%22breakReward%22%3A0%7D&client=apple&clientVersion=9.0.8&openudid=1fce88cd05c42fe2b054e846f11bdf33f016d676&sign=7e2f8bcec13978a691567257af4fdce9&st=1596954745073&sv=111',
            'headers' => [
                'Cookie' => $this->KEY,
            ],
        ];
        $nobyda = new nobyda();
        $nobyda->get($this->GetCashUrl, function ($error, $response, $data) use ($nobyda, $stop) {
            try {
                if ($error) {
                    throw new InternalServerErrorException(T($error));
                } else {
                    $Details = $this->LogDetails ? "response:\n" . $data : '';
                    $cc = json_decode($data, true);
                    if ($cc['data']['success']) {
                        DI()->logger->info("京东商城-现金签到成功 " . $Details);
                        $merge['JDGetCash']['success'] = 1;
                        if ($cc['data']['result'] && $cc['data']['result']['signCash']) {
                            $merge['JDGetCash']['Cash'] = $cc['data']['result']['signCash'];
                            $merge['JDGetCash']['notify'] = "京东商城-现金: 成功, 明细: " . $merge['JDGetCash']['Cash'] . "现金 💰";
                        } else {
                            $merge['JDGetCash']['notify'] = "京东商城-现金: 成功, 明细: 无现金 💰";
                        }
                    } else {
                        DI()->logger->info("京东商城-现金签到失败 " . $Details);
                        $merge['JDGetCash']['fail'] = 1;
                        if (preg_match('/\"bizCode\":201|已经签过/', $data)) {
                            $merge['JDGetCash']['notify'] = "京东商城-现金: 失败, 原因: 已签过 ⚠️";
                        } else if (preg_match('/\"code\":300|退出登录/', $data)) {
                            $merge['JDGetCash']['notify'] = "京东商城-现金: 失败, 原因: Cookie失效‼️";
                        } else {
                            $merge['JDGetCash']['notify'] = "京东商城-现金: 失败, 原因: 未知 ⚠️";
                        }
                    }
                }
            } catch (\Exception $eor) {
                $nobyda->AnError('京东商城-现金', 'JDGetCash', $eor);
            } finally {
                return;
            }

        });
    }

}
