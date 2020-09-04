<?php

namespace Library\JDDailyBonus;

use Library\Exception\InternalServerErrorException;
use function Common\DI;
use function PhalApi\T;

/**
 * 京东抽大奖
 * Class JingDongPrize
 * @package Library\JDDailyBonus
 */
class JingDongPrize
{
    private $JDkey;
    private $KEY;
    private $LogDetails = false; //是否开启响应日志, true则开启

    public function __construct()
    {
    }

    public function main($stop = 0)
    {
        usleep($stop * 1000);
        $this->JDkey = [
            'url' => 'https://api.m.jd.com/client.action?functionId=vvipscdp_raffleAct_index&client=apple&clientVersion=8.1.0&appid=member_benefit_m',
            'headers' => [
                'Cookie' => $this->KEY,
                'Referer' => 'https://jdmall.m.jd.com/beansForPrizes',
            ],
        ];
        $nobyda = new nobyda();
        $nobyda->get($this->JDkey, function ($error, $response, $data) use ($nobyda, $stop) {
            try {
                if ($error) {
                    throw new InternalServerErrorException(T($error));
                } else {
                    $Details = $this->LogDetails ? "response:\n" . $data : '';
                    if (preg_match('/\"raffleActKey\":\"[a-zA-z0-9]{3,}\"/', $data)) {
                        $cc = json_decode($data, true);
                        $merge['JDPrize']['key'] = $cc['data']['floorInfoList'][0]['detail']['raffleActKey'];
                        DI()->logger->info("京东商城-大奖查询成功 " . $Details);
                        if ($merge['JDPrize']['key']) {
                            call_user_func([new JDPrizeCheckin,'main'],$stop, $merge['JDPrize']['key']);
                        } else {
                            $merge['JDPrize']['notify'] = "京东商城-大奖: 失败, 原因: 无奖池 ⚠️";
                            $merge['JDPrize']['fail'] = 1;
                        }
                    } else {
                        DI()->logger->info("京东商城-大奖查询KEY失败 " . $Details);
                        if (preg_match('/(未登录|\"101\")/', $data)) {
                            $merge['JDPrize']['notify'] = "京东大奖-登录: 失败, 原因: Cookie失效‼️";
                            $merge['JDPrize']['fail'] = 1;
                        } else {
                            $merge['JDPrize']['notify'] = "京东大奖-登录: 失败, 原因: 未知 ⚠️";
                            $merge['JDPrize']['fail'] = 1;
                        }
                    }
                }
            } catch (\Exception $eor) {
                $nobyda->AnError('京东大奖-查询', 'JDPrize', $eor);
            } finally {
                return;
            }

        });
    }

}
