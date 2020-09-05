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
        $subsidyUrl = [
            'url' => 'https://ms.jr.jd.com/gw/generic/uc/h5/m/signIn7',
            'headers' => [
                'Cookie' => $this->initial->KEY,
                'Referer' => 'https://active.jd.com/forever/cashback/index',
            ],
        ];
        $this->initial->custom->get($subsidyUrl, function ($error, $response, $data) use ($stop) {
            try {
                if ($error) {
                    throw new InternalServerErrorException(T($error));
                } else {
                    $Details = $this->initial->LogDetails ? "response:\n" . $data : '';
                    if (preg_match('/\"msg\":\"操作成功\"/', $data)) {
                        $this->initial->custom->log("京东商城-金贴签到成功 " . $Details);
                        $this->initial->merge['subsidy']['success'] = 1;
                        if (preg_match('/\"thisAmountStr\":\".+?\"/', $data)) {
                            preg_match('/\"thisAmountStr\":\"(.+?)\"/', $data, $matches);
                            $Quantity = $matches[1];
                            $this->initial->merge['subsidy']['notify'] = "京东商城-金贴: 成功, 明细: " . $Quantity . "金贴 💰";
                        } else {
                            $this->initial->merge['subsidy']['notify'] = "京东商城-金贴: 成功, 明细: 无金贴 💰";
                        }
                    } else {
                        $this->initial->custom->log("京东商城-金贴签到失败 " . $Details);
                        $this->initial->merge['subsidy']['fail'] = 1;
                        if (preg_match('/已存在/', $data)) {
                            $this->initial->merge['subsidy']['notify'] = "京东商城-金贴: 失败, 原因: 已签过 ⚠️";
                        } else if (preg_match('/请先登录/', $data)) {
                            $this->initial->merge['subsidy']['notify'] = "京东商城-金贴: 失败, 原因: Cookie失效‼️";
                        } else {
                            $this->initial->merge['subsidy']['notify'] = "京东商城-金贴: 失败, 原因: 未知 ⚠️";
                        }
                    }
                }
            } catch (\Exception $eor) {
                $this->initial->custom->AnError('京东商城-金贴', 'subsidy', $eor);
            } finally {
                return;
            }

        });
    }

}
