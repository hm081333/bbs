<?php

namespace Library\JDDailyBonus;

use Library\Exception\InternalServerErrorException;
use function Common\DI;
use function PhalApi\T;

/**
 * 京东国际
 * Class JDOverseas
 * @package Library\JDDailyBonus
 */
class JDOverseas
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
        $OverseasUrl = [
            'url' => 'https://api.m.jd.com/client.action?functionId=checkin',
            'headers' => [
                "Content-Type" => "application/x-www-form-urlencoded",
                'Cookie' => $this->initial->KEY,
            ],
            'body' => 'body=%7B%7D&build=167237&client=apple&clientVersion=9.0.0&openudid=1fce88cd05c42fe2b054e846f11bdf33f016d676&partner=apple&scope=11&sign=e27f8b904040a0e3c99b87fc27e09c87&st=1591730990449&sv=101',
        ];
        $this->initial->custom->post($OverseasUrl, function ($error, $response, $data) use ($stop) {
            try {
                if ($error) {
                    throw new InternalServerErrorException(T($error));
                } else {
                    $Details = $this->initial->LogDetails ? "response:\n" . $data : '';
                    if (preg_match('/\"type\":\d+?,/', $data)) {
                        $this->initial->custom->log("京东商城-国际签到成功 " . $Details);
                        $this->initial->merge['Overseas']['success'] = 1;
                        if (preg_match('/\"jdBeanAmount\":[1-9]+/', $data)) {
                            preg_match('/\"jdBeanAmount\":(\d+)/', $data, $matches);
                            $this->initial->merge['Overseas']['bean'] = $matches[1];
                            $this->initial->merge['Overseas']['notify'] = "京东商城-国际: 成功, 明细: " . $this->initial->merge['Overseas']['bean'] . "京豆 🐶";
                        } else {
                            $this->initial->merge['Overseas']['notify'] = "京东商城-国际: 成功, 明细: 无京豆 🐶";
                        }
                    } else {
                        $this->initial->custom->log("京东商城-国际签到失败 " . $Details);
                        $this->initial->merge['Overseas']['fail'] = 1;
                        if (preg_match('/(\"code\":\"13\"|重复签到)/', $data)) {
                            $this->initial->merge['Overseas']['notify'] = "京东商城-国际: 失败, 原因: 已签过 ⚠️";
                        } else if (preg_match('/\"code\":\"-1\"/', $data)) {
                            $this->initial->merge['Overseas']['notify'] = "京东商城-国际: 失败, 原因: Cookie失效‼️";
                        } else {
                            $this->initial->merge['Overseas']['notify'] = "京东商城-国际: 失败, 原因: 未知 ⚠️";
                        }
                    }
                }
            } catch (\Exception $eor) {
                $this->initial->custom->AnError('京东商城-国际', 'Overseas', $eor);
            } finally {
                return;
            }

        });
    }

}
