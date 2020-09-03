<?php

namespace Library\JDDailyBonus;

use Library\Exception\InternalServerErrorException;
use function Common\DI;
use function PhalApi\T;

/**
 * 京东现金红包
 * Class JingDongCash
 * @package Library\JDDailyBonus
 */
class JingDongCash
{
    private $JDCAUrl;

    private $KEY;
    private $LogDetails = false; //是否开启响应日志, true则开启

    public function __construct($stop = 0)
    {
        sleep($stop);
        $this->JDCAUrl = [
            'url' => 'https://api.m.jd.com/client.action?functionId=ccSignInNew',
            'headers' => [
                "Content-Type" => "application/x-www-form-urlencoded",
                'Cookie' => $this->KEY,
            ],
            'body' => 'body=%7B%22pageClickKey%22%3A%22CouponCenter%22%2C%22eid%22%3A%22O5X6JYMZTXIEX4VBCBWEM5PTIZV6HXH7M3AI75EABM5GBZYVQKRGQJ5A2PPO5PSELSRMI72SYF4KTCB4NIU6AZQ3O6C3J7ZVEP3RVDFEBKVN2RER2GTQ%22%2C%22shshshfpb%22%3A%22v1%5C%2FzMYRjEWKgYe%2BUiNwEvaVlrHBQGVwqLx4CsS9PH1s0s0Vs9AWk%2B7vr9KSHh3BQd5NTukznDTZnd75xHzonHnw%3D%3D%22%2C%22childActivityUrl%22%3A%22openapp.jdmobile%253a%252f%252fvirtual%253fparams%253d%257b%255c%2522category%255c%2522%253a%255c%2522jump%255c%2522%252c%255c%2522des%255c%2522%253a%255c%2522couponCenter%255c%2522%257d%22%2C%22monitorSource%22%3A%22cc_sign_ios_index_config%22%7D&client=apple&clientVersion=8.5.0&d_brand=apple&d_model=iPhone8%2C2&openudid=1fce88cd05c42fe2b054e846f11bdf33f016d676&scope=11&screen=1242%2A2208&sign=1cce8f76d53fc6093b45a466e93044da&st=1581084035269&sv=102',
        ];
        $nobyda = new nobyda();
        $nobyda->post($this->JDCAUrl, function ($error, $response, $data) use ($nobyda, $stop) {
            try {
                if ($error) {
                    throw new InternalServerErrorException(T($error));
                } else {
                    $Details = $this->LogDetails ? "response:\n" . $data : '';
                    $cc = json_decode($data, true);
                    if ($cc['busiCode'] == "0") {
                        DI()->logger->info("京东现金-红包签到成功 ".$Details);
                            if ($cc['result']['signResult']['signData']['amount']) {
                                $merge['JDCash']['notify'] = "京东现金-红包: 成功, 明细: ".$cc['result']['signResult']['signData']['amount']."红包 🧧";
                                $merge['JDCash']['Cash'] = $cc['result']['signResult']['signData']['amount'];
                                $merge['JDCash']['success'] = 1;
                            } else {
                                $merge['JDCash']['notify'] = "京东现金-红包: 成功, 明细: 无红包 🧧";
                                $merge['JDCash']['success'] = 1;
                            }
                        } else {
                        DI()->logger->info("京东现金-红包签到失败 ".$Details);
                            if (preg_match('/(\"busiCode\":\"1002\"|完成签到)/', $data)) {
                                $merge['JDCash']['notify'] = "京东现金-红包: 失败, 原因: 已签过 ⚠️";
                                $merge['JDCash']['fail'] = 1;
                            } else {
                                if (preg_match('/(不存在|已结束)/', $data)) {
                                    $merge['JDCash']['notify'] = "京东现金-红包: 失败, 原因: 活动已结束 ⚠️";
                                    $merge['JDCash']['fail'] = 1;
                                } else {
                                    if (preg_match('/(\"busiCode\":\"3\"|未登录)/', $data)) {
                                        $merge['JDCash']['notify'] = "京东现金-红包: 失败, 原因: Cookie失效‼️";
                                        $merge['JDCash']['fail'] = 1;
                                    } else {
                                        $merge['JDCash']['notify'] = "京东现金-红包: 失败, 原因: 未知 ⚠️";
                                        $merge['JDCash']['fail'] = 1;
                                    }
                                }
                            }
                        }
                }
            } catch (\Exception $eor) {
                $nobyda->AnError('京东现金-红包', 'JDCash', $eor);
            } finally {
                return;
            }

        });
    }

}
