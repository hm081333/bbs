<?php

namespace Library\JDDailyBonus;

use Library\Exception\InternalServerErrorException;
use function Common\DI;
use function PhalApi\T;

/**
 * 京东转盘
 * Class JingDongTurn
 * @package Library\JDDailyBonus
 */
class JingDongTurn
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
        $JDTUrl = [
            'url' => 'https://api.m.jd.com/client.action?functionId=wheelSurfIndex&body=%7B%22actId%22%3A%22jgpqtzjhvaoym%22%2C%22appSource%22%3A%22jdhome%22%7D&appid=ld',
            'headers' => [
                'Cookie' => $this->initial->KEY,
            ],
        ];
        $this->initial->custom->get($JDTUrl, function ($error, $response, $data) use ( $stop) {
            try {
                if ($error) {
                    throw new InternalServerErrorException(T($error));
                } else {
                    $cc = json_decode($data, true)['data']['lotteryCode'];
                    $Details = $this->initial->LogDetails ? "response:\n" . $data : '';
                    if ($cc) {
                        $this->initial->custom->log("京东商城-转盘查询成功 " . $Details);
                        return call_user_func([new JingDongTurnSign($this->initial), 'main'], $stop, $cc);
                    } else {
                        $this->initial->merge['JDTurn']['notify'] = "京东商城-转盘: 失败, 原因: 查询错误 ⚠️";
                        $this->initial->merge['JDTurn']['fail'] = 1;
                        $this->initial->custom->log("京东商城-转盘查询失败 " . $Details);
                    }
                }
            } catch (\Exception $eor) {
                $this->initial->custom->AnError('京东转盘-查询', 'JDTurn', $eor);
            } finally {
                return;
            }

        });
    }

}
