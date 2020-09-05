<?php

namespace Library\JDDailyBonus;

use Library\Exception\InternalServerErrorException;
use function Common\DI;
use function PhalApi\T;

/**
 * 京东转盘 签到
 * Class JingDongTurnSign
 * @package Library\JDDailyBonus
 */
class JingDongTurnSign
{
    /**
     * @var initial
     */
    private $initial; // 初始化参数

    public function __construct(initial $initial)
    {
        $this->initial = $initial;
    }

    public function main($stop = 0, $code = false)
    {
        usleep($stop * 1000);
        $JDTUrl = [
            'url' => "https://api.m.jd.com/client.action?functionId=lotteryDraw&body=%7B%22actId%22%3A%22jgpqtzjhvaoym%22%2C%22appSource%22%3A%22jdhome%22%2C%22lotteryCode%22%3A%22{$code}%22%7D&appid=ld",
            'headers' => [
                'Cookie' => $this->initial->KEY,
            ],
        ];
        $this->initial->custom->get($JDTUrl, function ($error, $response, $data) use ($stop, $code) {
            try {
                if ($error) {
                    throw new InternalServerErrorException(T($error));
                } else {
                    $cc = json_decode($data, true);
                    $Details = $this->initial->LogDetails ? "response:\n" . $data : '';

                    if ($cc['code'] == 3) {
                        $this->initial->custom->log("京东转盘Cookie失效 " . $Details);
                        $this->initial->merge['JDTurn']['notify'] = "京东商城-转盘: 失败, 原因: Cookie失效‼️";
                        $this->initial->merge['JDTurn']['fail'] = 1;
                    } else {
                        if (preg_match('/(\"T216\"|活动结束)/', $data)) {
                            $this->initial->merge['JDTurn']['notify'] = "京东商城-转盘: 失败, 原因: 活动结束 ⚠️";
                            $this->initial->merge['JDTurn']['fail'] = 1;
                        } else {
                            if (preg_match('/(京豆|\"910582\")/', $data)) {
                                $this->initial->custom->log("京东商城-转盘签到成功 " . $Details);
                                $this->initial->merge['JDTurn']['notify'] .= $this->initial->merge['JDTurn']['notify'] ? "\n京东商城-转盘: 成功, 明细: " . $cc['data']['prizeSendNumber'] . "京豆 🐶 (多次)" : "京东商城-转盘: 成功, 明细: " . $cc['data']['prizeSendNumber'] . "京豆 🐶";
                                $this->initial->merge['JDTurn']['success'] += 1;
                                $this->initial->merge['JDTurn']['bean'] += $cc['data']['prizeSendNumber'];
                                if ($cc['data']['chances'] != "0") {
                                    call_user_func([new JingDongTurnSign($this->initial), 'main'], 2000, $code);
                                }
                            } else {
                                $this->initial->custom->log("京东商城-转盘签到失败 " . $Details);
                                if (preg_match('/未中奖/', $data)) {
                                    $this->initial->merge['JDTurn']['notify'] .= $this->initial->merge['JDTurn']['notify'] ? "\n京东商城-转盘: 成功, 状态: 未中奖 🐶 (多次)" : "京东商城-转盘: 成功, 状态: 未中奖 🐶";
                                    $this->initial->merge['JDTurn']['success'] += 1;
                                    if ($cc['data']['chances'] != "0") {
                                        call_user_func([new JingDongTurnSign($this->initial), 'main'], 2000, $code);
                                    }
                                } else if (preg_match('/(T215|次数为0)/', $data)) {
                                    $this->initial->merge['JDTurn']['notify'] = "京东商城-转盘: 失败, 原因: 已转过 ⚠️";
                                    $this->initial->merge['JDTurn']['fail'] = 1;
                                } else if (preg_match('/(T210|密码)/', $data)) {
                                    $this->initial->merge['JDTurn']['notify'] = "京东商城-转盘: 失败, 原因: 无支付密码 ⚠️";
                                    $this->initial->merge['JDTurn']['fail'] = 1;
                                } else {
                                    $this->initial->merge['JDTurn']['notify'] .= $this->initial->merge['JDTurn']['notify'] ? "\n京东商城-转盘: 失败, 原因: 未知 ⚠️ (多次)" : "京东商城-转盘: 失败, 原因: 未知 ⚠️";
                                    $this->initial->merge['JDTurn']['fail'] += 1;
                                }
                            }
                        }
                    }
                }
            } catch (\Exception $eor) {
                $this->initial->custom->AnError('京东商城-转盘', 'JDTurn', $eor);
            } finally {
                return;
            }

        });
    }

}
