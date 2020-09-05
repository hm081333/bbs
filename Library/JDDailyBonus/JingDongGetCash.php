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
        $GetCashUrl = [
            'url' => 'https://api.m.jd.com/client.action?functionId=cash_sign&body=%7B%22remind%22%3A0%2C%22inviteCode%22%3A%22%22%2C%22type%22%3A0%2C%22breakReward%22%3A0%7D&client=apple&clientVersion=9.0.8&openudid=1fce88cd05c42fe2b054e846f11bdf33f016d676&sign=7e2f8bcec13978a691567257af4fdce9&st=1596954745073&sv=111',
            'headers' => [
                'Cookie' => $this->initial->KEY,
            ],
        ];
        $this->initial->custom->get($GetCashUrl, function ($error, $response, $data) use ($stop) {
            try {
                if ($error) {
                    throw new InternalServerErrorException(T($error));
                } else {
                    $Details = $this->initial->LogDetails ? "response:\n" . $data : '';
                    $cc = json_decode($data, true);
                    if ($cc['data']['success']) {
                        $this->initial->custom->log("京东商城-现金签到成功 " . $Details);
                        $this->initial->merge['JDGetCash']['success'] = 1;
                        if ($cc['data']['result'] && $cc['data']['result']['signCash']) {
                            $this->initial->merge['JDGetCash']['Cash'] = $cc['data']['result']['signCash'];
                            $this->initial->merge['JDGetCash']['notify'] = "京东商城-现金: 成功, 明细: " . $this->initial->merge['JDGetCash']['Cash'] . "现金 💰";
                        } else {
                            $this->initial->merge['JDGetCash']['notify'] = "京东商城-现金: 成功, 明细: 无现金 💰";
                        }
                    } else {
                        $this->initial->custom->log("京东商城-现金签到失败 " . $Details);
                        $this->initial->merge['JDGetCash']['fail'] = 1;
                        if (preg_match('/\"bizCode\":201|已经签过/', $data)) {
                            $this->initial->merge['JDGetCash']['notify'] = "京东商城-现金: 失败, 原因: 已签过 ⚠️";
                        } else if (preg_match('/\"code\":300|退出登录/', $data)) {
                            $this->initial->merge['JDGetCash']['notify'] = "京东商城-现金: 失败, 原因: Cookie失效‼️";
                        } else {
                            $this->initial->merge['JDGetCash']['notify'] = "京东商城-现金: 失败, 原因: 未知 ⚠️";
                        }
                    }
                }
            } catch (\Exception $eor) {
                $this->initial->custom->AnError('京东商城-现金', 'JDGetCash', $eor);
            } finally {
                return;
            }

        });
    }

}
