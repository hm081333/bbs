<?php

namespace Library\JDDailyBonus;

use Library\Exception\InternalServerErrorException;
use function Common\DI;
use function PhalApi\T;

/**
 * 京东京豆
 * Class JingDongBean
 * @package Library\JDDailyBonus
 */
class JingDongBean
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
        $JDBUrl = [
            'url' => 'https://api.m.jd.com/client.action',
            'headers' => [
                'Cookie' => $this->initial->KEY,
            ],
            'body' => 'functionId=signBeanIndex&appid=ld'
        ];
        $this->initial->custom->post($JDBUrl, function ($error, $response, $data) use ($stop) {
            try {
                if ($error) {
                    throw new InternalServerErrorException(T($error));
                } else {
                    $cc = json_decode($data, true);
                    $Details = $this->initial->LogDetails ? "response:\n" . $data : '';
                    if ($cc['code'] == 3) {
                        $this->initial->custom->log("京东商城-京豆Cookie失效 " . $Details);
                        $this->initial->merge->JDBean->notify = "京东商城-京豆: 失败, 原因: Cookie失效‼️";
                        $this->initial->merge->JDBean->fail = 1;
                    } else if (preg_match('/跳转至拼图/', $data)) {
                        $this->initial->merge->JDBean->notify = "京东商城-京豆: 失败, 需要拼图验证 ⚠️";
                        $this->initial->merge->JDBean->fail = 1;
                    } else if (preg_match('/\"status\":\"?1\"?/', $data)) {
                        $this->initial->custom->log("京东商城-京豆签到成功 " . $Details);
                        if (preg_match('/dailyAward/', $data)) {
                            $this->initial->merge->JDBean->notify = "京东商城-京豆: 成功, 明细: " . $cc['data']['dailyAward']['beanAward']['beanCount'] . "京豆 🐶";
                            $this->initial->merge->JDBean->bean = $cc['data']['dailyAward']['beanAward']['beanCount'];
                        } else if (preg_match('/continuityAward/', $data)) {
                            $this->initial->merge->JDBean->notify = "京东商城-京豆: 成功, 明细: " . $cc['data']['continuityAward']['beanAward']['beanCount'] . "京豆 🐶";
                            $this->initial->merge->JDBean->bean = $cc['data']['continuityAward']['beanAward']['beanCount'];
                        } else if (preg_match('/新人签到/', $data)) {
                            $quantity = preg_match('/beanCount\":\"(\d+)\".+今天/', $data);
                            $this->initial->merge->JDBean->bean = $quantity ? $quantity[1] : 0;
                            $this->initial->merge->JDBean->notify = "京东商城-京豆: 成功, 明细: " . ($quantity ? $quantity[1] : "无") . "京豆 🐶";
                        } else {
                            $this->initial->merge->JDBean->notify = "京东商城-京豆: 成功, 明细: 无京豆 🐶";
                        }
                        $this->initial->merge->JDBean->success = 1;
                    } else {
                        $this->initial->merge->JDBean->fail = 1;
                        $this->initial->custom->log("京东商城-京豆签到失败 " . $Details);
                        if (preg_match('/(已签到|新人签到)/', $data)) {
                            $this->initial->merge->JDBean->notify = "京东商城-京豆: 失败, 原因: 已签过 ⚠️";
                        } else if (preg_match('/人数较多|S101/', $data)) {
                            $this->initial->merge->JDBean->notify = "京东商城-京豆: 失败, 签到人数较多 ⚠️";
                        } else {
                            $this->initial->merge->JDBean->notify = "京东商城-京豆: 失败, 原因: 未知 ⚠️";
                        }
                    }
                }
            } catch (\Exception $eor) {
                $this->initial->custom->AnError('京东商城-京豆', 'JDBean', $eor);
            } finally {
                return;
            }

        });
    }

}
