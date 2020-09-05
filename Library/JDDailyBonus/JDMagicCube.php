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

    public function main($stop = 0)
    {
        usleep($stop * 1000);
        $JDUrl = [
            'url' => 'https://api.m.jd.com/client.action?functionId=getNewsInteractionInfo&appid=smfe',
            'headers' => [
                'Cookie' => $this->initial->KEY,
            ],
        ];
        $this->initial->custom->get($JDUrl, function ($error, $response, $data) use ($stop) {
            try {
                if ($error) throw new InternalServerErrorException(T($error));
                if (preg_match('/\"interactionId\":\d+/', $data)) {
                    $Details = $this->initial->LogDetails ? "response:\n" . $data : '';
                    preg_match('/\"interactionId\":(\d+)/', $data, $matches);
                    $this->initial->merge['JDCube']['key'] = $matches[1];
                    $this->initial->custom->log('京东魔方-查询活动成功 ' . $Details);
                    call_user_func([new JDMagicCubeSign($this->initial), 'main'], $stop, $this->initial->merge['JDCube']['key']);
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
