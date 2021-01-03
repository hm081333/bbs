<?php

namespace Library\JDDailyBonus;

use Library\Exception\InternalServerErrorException;
use function Common\DI;
use function PhalApi\T;

/**
 * 京东小魔方
 * Class JDMagicCube
 * @package Library\JDDailyBonus
 */
class JDMagicCube
{
    /**
     * @var initial
     */
    private $initial; // 初始化参数

    public function __construct(initial $initial)
    {
        $this->initial = $initial;
    }

    public function main($stop = 0, $sign = null)
    {
        usleep($stop * 1000);
        $JDUrl = [
            'url' => 'https://api.m.jd.com/client.action?functionId=getNewsInteractionInfo&appid=smfe' . ($sign ? '&body=' . urlencode(json_encode(['sign' => $sign])) : ''),
            'headers' => [
                'Cookie' => $this->initial->KEY,
            ],
        ];
        $this->initial->custom->get($JDUrl, function ($error, $response, $data) use ($stop, $sign) {
            try {
                if ($error) throw new InternalServerErrorException(T($error));
                $Details = $this->initial->LogDetails ? "response:\n" . $data : '';
                $this->initial->custom->log("京东魔方-尝试查询活动({$sign}) {$Details}");
                if (preg_match('/\"interactionId\":\d+/', $data)) {
                    preg_match('/\"interactionId\":(\d+)/', $data, $matches);
                    $this->initial->merge->JDCube->key = $matches[1];
                    $this->initial->custom->log('京东魔方-查询活动成功 ' . $Details);
                    call_user_func([new JDMagicCubeSign($this->initial), 'main'], $stop, [
                        'id' => $this->initial->merge->JDCube->key,
                        'sign' => $sign
                    ]);
                } else if (preg_match('/配置异常/', $data) && $sign) {
                    call_user_func([new JDMagicCube($this->initial), 'main'], $stop, $sign == 2 ? 1 : null);
                } else {
                    $this->initial->custom->log('京东魔方-查询活动失败 ');
                }
            } catch (\Exception $eor) {
                $this->initial->custom->AnError('京东魔方-查询', 'JDCube', $eor);
            } finally {
                return;
            }

        });
    }

}
