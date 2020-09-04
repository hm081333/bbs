<?php

namespace Library\JDDailyBonus;

use Library\Exception\InternalServerErrorException;
use function Common\DI;
use function PhalApi\T;

/**
 * 查询道具-加速
 * Class JDQueryTask
 * @package Library\JDDailyBonus
 */
class JDQueryTask
{
    private $QueryUrl;
    private $KEY;
    private $LogDetails = false; //是否开启响应日志, true则开启

    public function __construct()
    {
    }

    public function main($stop = 0)
    {
        usleep($stop * 1000);
        $TaskID = '';
        $this->QueryUrl = [
            'url' => 'https://api.m.jd.com/?appid=memberTaskCenter&functionId=energyProp_list&body=%7B%22source%22%3A%22game%22%7D',
            'headers' => [
                'Cookie' => $this->KEY,
                'Referer' => 'https://h5.m.jd.com/babelDiy/Zeus/6yCQo2eDJPbyPXrC3eMCtMWZ9ey/index.html',
            ],
        ];
        $nobyda = new nobyda();
        $nobyda->get($this->QueryUrl, function ($error, $response, $data) use ($nobyda, $stop, $TaskID) {
            try {
                if ($error) {
                    throw new InternalServerErrorException(T($error));
                } else {
                    $Details = $this->LogDetails ? "response:\n" . $data : '';
                    $cc = json_decode($data, true);
                    if ($cc['message'] == "success" && count($cc['data']) > 0) {
                        for ($i = 0; $i < count($cc['data']); $i++) {
                            if ($cc['data'][$i]['thaw_time'] == 0) {
                                $TaskID .= $cc['data'][$i]['id'] . ",";
                            }
                        }
                        if (strlen($TaskID) > 0) {
                            $TaskID = explode(substr($TaskID, 0, strlen($TaskID) - 1), ',');
                            DI()->logger->info("天天加速-查询到" . count($TaskID) . "个有效道具" . $Details);
                        } else {
                            DI()->logger->info("天天加速-暂无有效道具" . $Details);
                        }
                    } else {
                        DI()->logger->info("天天加速-查询无道具" . $Details);
                    }
                }
            } catch (\Exception $eor) {
                $nobyda->AnError('查询道具-加速', 'SpeedUp', $eor);
            } finally {
                return $TaskID;
            }

        });
    }

}
