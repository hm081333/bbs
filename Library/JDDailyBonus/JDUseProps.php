<?php

namespace Library\JDDailyBonus;

use Library\Exception\InternalServerErrorException;
use function Common\DI;
use function PhalApi\T;

/**
 * 使用道具-加速
 * Class JDUseProps
 * @package Library\JDDailyBonus
 */
class JDUseProps
{
    /**
     * @var initial
     */
    private $initial; // 初始化参数

    public function __construct(initial $initial)
    {
        $this->initial = $initial;
    }

    public function main($stop = 0, $PropID = [])
    {
        if ($PropID) {
            usleep($stop * 1000);
            $PropCount = 0;
            $PropNumTask = 0;
            for ($i = 0; $i < count($PropID); $i++) {
                $PropUrl = [
                    'url' => 'https://api.m.jd.com/?appid=memberTaskCenter&functionId=energyProp_use&body=%7B%22source%22%3A%22game%22%2C%22energy_id%22%3A%22' . $PropID[$i] . '%22%7D',
                    'headers' => [
                        'Cookie' => $this->initial->KEY,
                        'Referer' => 'https://h5.m.jd.com/babelDiy/Zeus/6yCQo2eDJPbyPXrC3eMCtMWZ9ey/index.html',
                    ],
                ];
                $this->initial->custom->get($PropUrl, function ($error, $response, $data) use ($stop, $PropID, $PropCount, $PropNumTask) {
                    try {
                        $PropCount++;
                        if ($error) {
                            throw new InternalServerErrorException(T($error));
                        } else {
                            $cc = json_encode($data, true);
                            $Details = $this->initial->LogDetails ? "response:\n" . $data : '';
                            $this->initial->custom->log('天天加速-尝试使用第' . $PropCount . "个道具" . $Details);
                            if ($cc['message'] == 'success' && $cc['success'] == true) {
                                $PropNumTask += 1;
                            }
                        }
                    } catch (\Exception $eor) {
                        $this->initial->custom->AnError("使用道具-加速", "SpeedUp", $eor);
                    } finally {
                        if (count($PropID) == $PropCount) {
                            $this->initial->custom->log("天天加速-已成功使用" . $PropNumTask . "个道具");
                            return;
                        }
                    }
                });
            }
        } else {
            return;
        }
    }

}
