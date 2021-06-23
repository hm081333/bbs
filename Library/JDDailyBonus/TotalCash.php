<?php

namespace Library\JDDailyBonus;

use Library\Exception\InternalServerErrorException;
use function Common\DI;
use function PhalApi\T;

/**
 * 总红包查询
 * Class TotalCash
 * @package Library\JDDailyBonus
 */
class TotalCash
{
    /**
     * @var initial
     */
    private $initial; // 初始化参数

    public function __construct(initial $initial)
    {
        $this->initial = $initial;
    }

    public function main()
    {
        $CashUrl = [
            'url' => 'https://api.m.jd.com/client.action?functionId=myhongbao_balance',
            'headers' => [
                "Content-Type" => "application/x-www-form-urlencoded",
                'Cookie' => $this->initial->KEY,
            ],
            'body' => "body=%7B%22fp%22%3A%22-1%22%2C%22appToken%22%3A%22apphongbao_token%22%2C%22childActivityUrl%22%3A%22-1%22%2C%22country%22%3A%22cn%22%2C%22openId%22%3A%22-1%22%2C%22childActivityId%22%3A%22-1%22%2C%22applicantErp%22%3A%22-1%22%2C%22platformId%22%3A%22appHongBao%22%2C%22isRvc%22%3A%22-1%22%2C%22orgType%22%3A%222%22%2C%22activityType%22%3A%221%22%2C%22shshshfpb%22%3A%22-1%22%2C%22platformToken%22%3A%22apphongbao_token%22%2C%22organization%22%3A%22JD%22%2C%22pageClickKey%22%3A%22-1%22%2C%22platform%22%3A%221%22%2C%22eid%22%3A%22-1%22%2C%22appId%22%3A%22appHongBao%22%2C%22childActiveName%22%3A%22-1%22%2C%22shshshfp%22%3A%22-1%22%2C%22jda%22%3A%22-1%22%2C%22extend%22%3A%22-1%22%2C%22shshshfpa%22%3A%22-1%22%2C%22activityArea%22%3A%22-1%22%2C%22childActivityTime%22%3A%22-1%22%7D&client=apple&clientVersion=8.5.0&d_brand=apple&networklibtype=JDNetworkBaseAF&openudid=1fce88cd05c42fe2b054e846f11bdf33f016d676&sign=fdc04c3ab0ee9148f947d24fb087b55d&st=1581245397648&sv=120",
        ];
        $this->initial->custom->post($CashUrl, function ($error, $response, $data) {
            try {
                if ($error) {
                    throw new InternalServerErrorException(T($error));
                } else {
                    $Details = $this->initial->LogDetails ? "response:\n" . $data : '';

                    if (preg_match('/(\"totalBalance\":\d+)/', $data)) {
                        $this->initial->custom->log("京东-总红包查询成功 " . $Details);
                        $cc = json_decode($data, true);
                        $this->initial->merge->JDCash->TCash = $cc['totalBalance'];
                    } else {
                        $this->initial->custom->log("京东-总红包查询失败 " . $Details);
                    }
                }
            } catch (\Exception $eor) {
                $this->initial->custom->AnError('账户红包-查询', 'JDCash', $eor);
            } finally {
                return;
            }

        });
    }

}
