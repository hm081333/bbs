<?php

namespace Library\JDDailyBonus;

use Library\Exception\InternalServerErrorException;
use function Common\DI;
use function PhalApi\T;

/**
 * 查询号码-加速
 * Class JDQueryTaskID
 * @package Library\JDDailyBonus
 */
class JDQueryTaskID
{
    /**
     * @var initial
     */
    private $initial; // 初始化参数

    public function __construct(initial $initial)
    {
        $this->initial = $initial;
    }

    public function main($stop = 0, $EID = [])
    {
        $TaskCID = '';
        if ($EID) {
            $stop += 200;
            usleep($stop * 1000);
            $EUrl = [
                'url' => 'https://api.m.jd.com/?appid=memberTaskCenter&functionId=energyProp_usalbeList&body=%7B%22source%22%3A%22game%22%7D',
                'headers' => [
                    'Cookie' => $this->initial->KEY,
                    'Referer' => 'https://h5.m.jd.com/babelDiy/Zeus/6yCQo2eDJPbyPXrC3eMCtMWZ9ey/index.html',
                ],
            ];
            $this->initial->custom->get($EUrl, function ($error, $response, $data) use ($stop, $EID, $TaskCID) {
                try {
                    if ($error) {
                        throw new InternalServerErrorException(T($error));
                    } else {
                        $cc = json_encode($data, true);
                        $Details = $this->initial->LogDetails ? "response:\n" . $data : '';
                        if (count($cc['data']) > 0) {
                            for ($i = 0; $i < count($cc['data']); $i++) {
                                if ($cc['data'][$i]['id']) {
                                    $TaskCID .= $cc['data'][$i]['id'] . ",";
                                }
                            }
                            if (strlen($TaskCID) > 0) {
                                $TaskCID = explode(substr($TaskCID, 0, strlen($TaskCID) - 1), ',');
                                $this->initial->custom->log("天天加速-查询成功" . count($TaskCID) . "个道具ID" . $Details);
                            } else {
                                $this->initial->custom->log("天天加速-暂无有效道具ID" . $Details);
                            }
                        } else {
                            $this->initial->custom->log("天天加速-查询无道具ID" . $Details);
                        }
                    }
                } catch (\Exception $eor) {
                    $this->initial->custom->AnError("查询号码-加速", "SpeedUp", $eor);
                } finally {
                    return $TaskCID;
                }
            });
        } else {
            return $TaskCID;
        }
    }

}
