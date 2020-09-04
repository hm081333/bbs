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
    private $JDUrl;
    private $KEY;
    private $LogDetails = false; //是否开启响应日志, true则开启

    public function __construct()
    {
    }

    public function main($stop = 0)
    {
        usleep($stop * 1000);
        $this->JDUrl = [
            'url' => 'https://api.m.jd.com/client.action?functionId=getNewsInteractionInfo&appid=smfe',
            'headers' => [
                'Cookie' => $this->KEY,
            ],
        ];
        $nobyda = new nobyda();
        $nobyda->get($this->JDUrl, function ($error, $response, $data) use ($nobyda, $stop) {
            try {
                if ($error) throw new InternalServerErrorException(T($error));
                if (preg_match('/\"interactionId\":\d+/', $data)) {
                    $Details = $this->LogDetails ? "response:\n" . $data : '';
                    preg_match('/\"interactionId\":(\d+)/', $data, $matches);
                    $merge['JDCube']['key'] = $matches[1];
                    DI()->logger->info('京东魔方-查询活动成功 ' . $Details);
                    call_user_func([new JDMagicCubeSign,'main'],$stop, $merge['JDCube']['key']);
                } else {
                    DI()->logger->info('京东魔方-查询活动失败 ');
                }
            } catch (\Exception $eor) {
                $nobyda->AnError('京东魔方-查询', 'JDCube', $eor);
            } finally {
                return;
            }

        });
    }

}
