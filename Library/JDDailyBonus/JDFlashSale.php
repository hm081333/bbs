<?php

namespace Library\JDDailyBonus;

use Library\Exception\InternalServerErrorException;
use function Common\DI;
use function PhalApi\T;

/**
 * 京东闪购
 * Class JDFlashSale
 * @package Library\JDDailyBonus
 */
class JDFlashSale
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
        $JDPETUrl = [
            'url' => 'https://api.m.jd.com/client.action?functionId=partitionJdSgin',
            'headers' => [
                "Content-Type" => "application/x-www-form-urlencoded",
                'Cookie' => $this->initial->KEY,
            ],
            'body' => 'body=%7B%22version%22%3A%22v2%22%7D&client=apple&clientVersion=9.0.8&openudid=1fce88cd05c42fe2b054e846f11bdf33f016d676&sign=6768e2cf625427615dd89649dd367d41&st=1597248593305&sv=121',
        ];
        $this->initial->custom->post($JDPETUrl, function ($error, $response, $data) use ($stop) {
            try {
                if ($error) {
                    throw new InternalServerErrorException(T($error));
                } else {
                    $cc = json_decode($data, true);
                    $Details = $this->initial->LogDetails ? "response:\n" . $data : '';
                    if ($cc['result']['code'] == 0) {
                        $this->initial->custom->log("京东商城-闪购签到成功 " . $Details);
                        $this->initial->merge['JDFSale']['bean'] = $cc['result']['jdBeanNum'] || 0;
                        $this->initial->merge['JDFSale']['notify'] = "京东商城-闪购: 成功, 明细: " . ($this->initial->merge['JDFSale']['bean'] || "无") . "京豆 🐶";
                        $this->initial->merge['JDFSale']['success'] = 1;
                    } else {
                        $this->initial->merge['JDFSale']['fail'] = 1;
                        $this->initial->custom->log("京东商城-闪购签到失败 " . $Details);
                        if (preg_match('/(已签到|已领取|\"2005\")/', $data)) {
                            $this->initial->merge['JDFSale']['notify'] = "京东商城-闪购: 失败, 原因: 已签过 ⚠️";
                        } else if (preg_match('/不存在|已结束|\"2008\"|\"3001\"/', $data)) {
                            //$this->initial->merge['JDFSale']['notify'] = "京东商城-闪购: 失败, 原因: 需瓜分 ⚠️";
                            call_user_func([new FlashSaleDivide($this->initial), 'main'], $stop);
                        } else if (preg_match('/(\"code\":\"3\"|\"1003\")/', $data)) {
                            $this->initial->merge['JDFSale']['notify'] = "京东商城-闪购: 失败, 原因: Cookie失效‼️";
                        } else {
                            $this->initial->merge['JDFSale']['notify'] = "京东商城-闪购: 失败, 原因: 未知 ⚠️";
                        }
                    }
                }
            } catch (\Exception $eor) {
                $this->initial->custom->AnError('京东商城-闪购', 'JDFSale', $eor);
            } finally {
                return;
            }

        });
    }

}
