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
    /**
     * @var initial
     */
    private $initial; // 初始化参数

    public function __construct(initial $initial)
    {
        $this->initial = $initial;
    }

    public function main($stop = 0, $CID = [])
    {
        $NumTask = 0;
        if ($CID) {
            usleep($stop * 1000);
            $count = 0;
            for ($i = 0; $i < count($CID); $i++) {
                $TUrl = [
                    'url' => 'https://api.m.jd.com/?appid=memberTaskCenter&functionId=energyProp_gain&body=%7B%22source%22%3A%22game%22%2C%22energy_id%22%3A' . $CID[$i] . '%7D',
                    'headers' => [
                        'Cookie' => $this->initial->KEY,
                        'Referer' => 'https://h5.m.jd.com/babelDiy/Zeus/6yCQo2eDJPbyPXrC3eMCtMWZ9ey/index.html',
                    ],
                ];
                $this->initial->custom->get($TUrl, function ($error, $response, $data) use ($stop, $CID, $NumTask, $count) {
                    try {
                        $count++;
                        if ($error) {
                            throw new InternalServerErrorException(T($error));
                        } else {
                            $cc = json_encode($data, true);
                            $Details = $this->initial->LogDetails ? "response:\n" . $data : '';
                            $this->initial->custom->log("天天加速-尝试领取第" . $count . "个道具" . $Details);
                            if ($cc['message'] == 'success') {
                                $NumTask += 1;
                            }
                        }
                    } catch (\Exception $eor) {
                        $this->initial->custom->AnError("领取道具-加速", "SpeedUp", $eor);
                    } finally {
                        if (count($CID) == $count) {
                            $this->initial->custom->log("天天加速-已成功领取" . $NumTask . "个道具");
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
