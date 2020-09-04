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
    private $EUrl;
    private $KEY;
    private $LogDetails = false; //是否开启响应日志, true则开启

    public function __construct()
    {
    }

    public function main($stop = 0, $EID = [])
    {
        $TaskCID = '';
        if ($EID) {
            $stop += 200;
            usleep($stop * 1000);
            $this->EUrl = [
                'url' => 'https://api.m.jd.com/?appid=memberTaskCenter&functionId=energyProp_usalbeList&body=%7B%22source%22%3A%22game%22%7D',
                'headers' => [
                    'Cookie' => $this->KEY,
                    'Referer' => 'https://h5.m.jd.com/babelDiy/Zeus/6yCQo2eDJPbyPXrC3eMCtMWZ9ey/index.html',
                ],
            ];
            $nobyda = new nobyda();
            $nobyda->get($this->EUrl, function ($error, $response, $data) use ($nobyda, $stop, $EID, $TaskCID) {
                try {
                    if ($error) {
                        throw new InternalServerErrorException(T($error));
                    } else {
                        $cc = json_encode($data, true);
                        $Details = $this->LogDetails ? "response:\n" . $data : '';
                        if (count($cc['data']) > 0) {
                            for ($i = 0; $i < count($cc['data']); $i++) {
                                if ($cc['data'][$i]['id']) {
                                    $TaskCID .= $cc['data'][$i]['id'] . ",";
                                }
                            }
                            if (strlen($TaskCID) > 0) {
                                $TaskCID = explode(substr($TaskCID, 0, strlen($TaskCID) - 1), ',');
                                DI()->logger->info("\n天天加速-查询成功" . count($TaskCID) . "个道具ID" . $Details);
                            } else {
                                DI()->logger->info("\n天天加速-暂无有效道具ID" . $Details);
                            }
                        } else {
                            DI()->logger->info("\n天天加速-查询无道具ID" . $Details);
                        }
                    }
                } catch (\Exception $eor) {
                    $nobyda->AnError("查询号码-加速", "SpeedUp", $eor);
                } finally {
                    return $TaskCID;
                }
            });
        } else {
            return $TaskCID;
        }
    }

}
