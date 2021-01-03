<?php

namespace Library\JDDailyBonus;

use Library\Exception\InternalServerErrorException;
use function Common\DI;
use function PhalApi\T;

/**
 * 京东闪购 瓜分签到
 * Class FlashSaleDivide
 * @package Library\JDDailyBonus
 */
class FlashSaleDivide
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
        $Url = [
            'url' => 'https://api.m.jd.com/client.action?functionId=partitionJdShare',
            'headers' => [
                "Content-Type" => "application/x-www-form-urlencoded",
                'Cookie' => $this->initial->KEY,
            ],
            'body' => 'body=%7B%22version%22%3A%22v2%22%7D&client=apple&clientVersion=9.0.8&openudid=1fce88cd05c42fe2b054e846f11bdf33f016d676&sign=49baa3b3899b02bbf06cdf41fe191986&st=1597682588351&sv=111',
        ];
        $this->initial->custom->post($Url, function ($error, $response, $data) use ($stop) {
            try {
                if ($error) {
                    throw new InternalServerErrorException(T($error));
                } else {
                    $Details = $this->initial->LogDetails ? "response:\n" . $data : '';
                    $cc = json_decode($data, true);
                    if ($cc['result']['code'] == 0) {
                        $this->initial->merge->JDFSale->success = 1;
                        $this->initial->merge->JDFSale->bean = $cc['result']['jdBeanNum'] ?: 0;
                        $this->initial->merge->JDFSale->notify = "京东闪购-瓜分: 成功, 明细: " . ($this->initial->merge->JDFSale->bean || "无") . "京豆 🐶";
                        $this->initial->custom->log("京东闪购-瓜分签到成功 " . $Details);
                    } else {
                        $this->initial->merge->JDFSale->fail = 1;
                        $this->initial->custom->log("京东闪购-瓜分签到失败 " . $Details);
                        if (preg_match('/已参与|已领取|\"2006\"/', $data)) {
                            $this->initial->merge->JDFSale->notify = "京东闪购-瓜分: 失败, 原因: 已瓜分 ⚠️";
                        } else if (preg_match('/不存在|已结束|未开始|\"2008\"|\"2012\"/', $data)) {
                            $this->initial->merge->JDFSale->notify = "京东闪购-瓜分: 失败, 原因: 活动已结束 ⚠️";
                        } else if (preg_match('/\"code\":\"1003\"|未获取/', $data)) {
                            $this->initial->merge->JDFSale->notify = "京东闪购-瓜分: 失败, 原因: Cookie失效‼️";
                        } else {
                            $this->initial->merge->JDFSale->notify = "京东闪购-瓜分: 失败, 原因: 未知 ⚠️";
                        }
                    }
                }
            } catch (\Exception $eor) {
                $this->initial->custom->AnError('京东闪购-瓜分', 'JDFSale', $eor);
            } finally {
                return;
            }

        });
    }

}
