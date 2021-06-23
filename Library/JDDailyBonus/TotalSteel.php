<?php

namespace Library\JDDailyBonus;

use Library\Exception\InternalServerErrorException;
use function Common\DI;
use function PhalApi\T;

/**
 * 总钢镚查询
 * Class TotalSteel
 * @package Library\JDDailyBonus
 */
class TotalSteel
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
        $SteelUrl = [
            'url' => 'https://coin.jd.com/m/gb/getBaseInfo.html',
            'headers' => [
                'Cookie' => $this->initial->KEY,
            ],
        ];
        $this->initial->custom->get($SteelUrl, function ($error, $response, $data) {
            try {
                if ($error) {
                    throw new InternalServerErrorException(T($error));
                } else {
                    $Details = $this->initial->LogDetails ? "response:\n" . $data : '';

                    if (preg_match('/(\"gbBalance\":\d+)/', $data)) {
                        $this->initial->custom->log("京东-总钢镚查询成功 " . $Details);
                        $cc = json_decode($data, true);
                        $this->initial->merge->JRSteel->TSteel = $cc['gbBalance'];
                    } else {
                        $this->initial->custom->log("京东-总钢镚查询失败 " . $Details);
                    }
                }
            } catch (\Exception $eor) {
                $this->initial->custom->AnError('账户钢镚-查询', 'JRSteel', $eor);
            } finally {
                return;
            }

        });
    }

}
