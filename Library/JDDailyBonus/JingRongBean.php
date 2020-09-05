<?php

namespace Library\JDDailyBonus;

use Library\Exception\InternalServerErrorException;
use function Common\DI;
use function PhalApi\T;

/**
 * 金融京豆
 * Class JingRongBean
 * @package Library\JDDailyBonus
 */
class JingRongBean
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
        $login = [
            'url' => 'https://ms.jr.jd.com/gw/generic/zc/h5/m/signRecords',
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Cookie' => $this->initial->KEY,
                'Referer' => 'https://jddx.jd.com/m/money/index.html?from=sign',
            ],
            'body' => 'reqData=%7B%22bizLine%22%3A2%7D',
        ];
        $this->initial->custom->post($login, function ($error, $response, $data) use ($stop) {
            try {
                if ($error) {
                    throw new InternalServerErrorException(T($error));
                } else {
                    $Details = $this->initial->LogDetails ? "response:\n" . $data : '';
                    if (preg_match('/\"login\":true/', $data)) {
                        $this->initial->custom->log("京东金融-金贴登录成功 " . $Details);
                        call_user_func([new JRBeanCheckin($this->initial), 'main'], 200);
                    } else {
                        $this->initial->custom->log("京东金融-金贴登录失败 " . $Details);
                        if (preg_match('/\"login\":false/', $data)) {
                            $this->initial->merge['JRBean']['notify'] = "京东金融-金贴: 失败, 原因: Cookie失效‼️";
                            $this->initial->merge['JRBean']['fail'] = 1;
                        } else {
                            $this->initial->merge['JRBean']['notify'] = "京东金融-金贴: 登录接口需修正 ‼️‼️";
                            $this->initial->merge['JRBean']['fail'] = 1;
                        }
                    }
                }
            } catch (\Exception $eor) {
                $this->initial->custom->AnError('金融金贴-登录', 'JRBean', $eor);
            } finally {
                return;
            }

        });
    }

}
