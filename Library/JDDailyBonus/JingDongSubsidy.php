<?php

namespace Library\JDDailyBonus;

use Library\Exception\InternalServerErrorException;
use function Common\DI;
use function PhalApi\T;

/**
 * 京东金贴
 * Class JingDongSubsidy
 * @package Library\JDDailyBonus
 */
class JingDongSubsidy
{
    private $subsidyUrl;
    private $KEY;
    private $LogDetails = false; //是否开启响应日志, true则开启

    public function __construct()
    {
    }

    public function main($stop = 0)
    {
        usleep($stop * 1000);
        $this->subsidyUrl = [
            'url' => 'https://ms.jr.jd.com/gw/generic/uc/h5/m/signIn7',
            'headers' => [
                'Cookie' => $this->KEY,
                'Referer' => 'https://active.jd.com/forever/cashback/index',
            ],
        ];
        $nobyda = new nobyda();
        $nobyda->get($this->subsidyUrl, function ($error, $response, $data) use ($nobyda, $stop) {
            try {
                if ($error) {
                    throw new InternalServerErrorException(T($error));
                } else {
                    $Details = $this->LogDetails ? "response:\n" . $data : '';
                    if (preg_match('/\"msg\":\"操作成功\"/', $data)) {
                        DI()->logger->info("京东商城-金贴签到成功 " . $Details);
                        $merge['subsidy']['success'] = 1;
                        if (preg_match('/\"thisAmountStr\":\".+?\"/', $data)) {
                            preg_match('/\"thisAmountStr\":\"(.+?)\"/', $data, $matches);
                            $Quantity = $matches[1];
                            $merge['subsidy']['notify'] = "京东商城-金贴: 成功, 明细: " . $Quantity . "金贴 💰";
                        } else {
                            $merge['subsidy']['notify'] = "京东商城-金贴: 成功, 明细: 无金贴 💰";
                        }
                    } else {
                        DI()->logger->info("京东商城-金贴签到失败 " . $Details);
                        $merge['subsidy']['fail'] = 1;
                        if (preg_match('/已存在/', $data)) {
                            $merge['subsidy']['notify'] = "京东商城-金贴: 失败, 原因: 已签过 ⚠️";
                        } else if (preg_match('/请先登录/', $data)) {
                            $merge['subsidy']['notify'] = "京东商城-金贴: 失败, 原因: Cookie失效‼️";
                        } else {
                            $merge['subsidy']['notify'] = "京东商城-金贴: 失败, 原因: 未知 ⚠️";
                        }
                    }
                }
            } catch (\Exception $eor) {
                $nobyda->AnError('京东商城-金贴', 'subsidy', $eor);
            } finally {
                return;
            }

        });
    }

}
