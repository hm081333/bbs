<?php

namespace Library\JDDailyBonus;

use Library\Exception\InternalServerErrorException;
use function Common\DI;
use function PhalApi\T;

/**
 * 总京豆查询
 * Class TotalBean
 * @package Library\JDDailyBonus
 */
class TotalBean
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
        $BeanUrl = [
            'url' => 'https://wq.jd.com/user/info/QueryJDUserInfo?sceneval=2',
            'headers' => [
                'Cookie' => $this->initial->KEY,
                'Referer' => 'https://wqs.jd.com/my/jingdou/my.shtml?sceneval=2',
            ],
        ];
        $this->initial->custom->post($BeanUrl, function ($error, $response, $data) {
            try {
                if ($error) {
                    throw new InternalServerErrorException(T($error));
                } else {
                    $Details = $this->initial->LogDetails ? "response:\n" . $data : '';
                    $cc = json_decode($data, true);
                    if ($cc['base']['jdNum'] != 0) {
                        $this->initial->custom->log("京东-总京豆查询成功 " . $Details);
                        $this->initial->merge->JDShake->Qbear = $cc['base']['jdNum'];
                    } else {
                        $this->initial->custom->log("京东-总京豆查询失败 " . $Details);
                    }
                    if (preg_match('/\"nickname\" ?: ?\"(.+?)\",/', $data)) {
                        $this->initial->merge->JDShake->nickname = $cc['base']['nickname'];
                    } else if (preg_match('/\"no ?login\.?\"/', $data)) {
                        $this->initial->merge->JDShake->nickname = "Cookie失效 ‼️";
                    } else {
                        $this->initial->merge->JDShake->nickname = '';
                    }
                }
            } catch (\Exception $eor) {
                $this->initial->custom->AnError('账户京豆-查询', 'JDShake', $eor);
            } finally {
                return;
            }

        });
    }

}
