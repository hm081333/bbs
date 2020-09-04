<?php

namespace Library\JDDailyBonus;

use Library\Exception\InternalServerErrorException;
use function Common\DI;
use function PhalApi\T;

/**
 * 领取道具-加速
 * Class JDReceiveTask
 * @package Library\JDDailyBonus
 */
class JDReceiveTask
{
    private $TUrl;
    private $KEY;
    private $LogDetails = false; //是否开启响应日志, true则开启

    public function __construct()
    {
    }

    public function main($stop = 0, $CID = [])
    {
        $NumTask = 0;
        if ($CID) {
            usleep($stop * 1000);
            $count = 0;
            $nobyda = new nobyda();
            for ($i = 0; $i < count($CID); $i++) {
                $this->TUrl = [
                    'url' => 'https://api.m.jd.com/?appid=memberTaskCenter&functionId=energyProp_gain&body=%7B%22source%22%3A%22game%22%2C%22energy_id%22%3A' . $CID[$i] . '%7D',
                    'headers' => [
                        'Cookie' => $this->KEY,
                        'Referer' => 'https://h5.m.jd.com/babelDiy/Zeus/6yCQo2eDJPbyPXrC3eMCtMWZ9ey/index.html',
                    ],
                ];
                $nobyda->get($this->TUrl, function ($error, $response, $data) use ($nobyda, $stop, $CID, $NumTask, $count) {
                    try {
                        $count++;
                        if ($error) {
                            throw new InternalServerErrorException(T($error));
                        } else {
                            $cc = json_encode($data, true);
                            $Details = $this->LogDetails ? "response:\n" . $data : '';
                            DI()->logger->info("天天加速-尝试领取第" . $count . "个道具" . $Details);
                            if ($cc['message'] == 'success') {
                                $NumTask += 1;
                            }
                        }
                    } catch (\Exception $eor) {
                        $nobyda->AnError("领取道具-加速", "SpeedUp", $eor);
                    } finally {
                        if (count($CID) == $count) {
                            DI()->logger->info("天天加速-已成功领取" . $NumTask . "个道具");
                            return $NumTask;
                        }
                    }
                });
            }
        } else {
            return $NumTask;
        }
    }

}
