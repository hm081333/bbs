<?php

namespace Library\JDDailyBonus;

use Library\Exception\InternalServerErrorException;
use function Common\DI;
use function PhalApi\T;

/**
 * 京东天天加速
 * Class JingDongSpeedUp
 * @package Library\JDDailyBonus
 */
class JingDongSpeedUp
{
    private $GameUrl;
    private $KEY;
    private $LogDetails = false; //是否开启响应日志, true则开启

    public function __construct()
    {
    }

    public function main($stop = 0, $id = false)
    {
        usleep($stop * 1000);
        $this->GameUrl = [
            'url' => 'https://api.m.jd.com/?appid=memberTaskCenter&functionId=flyTask_' . ($id ? 'start&body=%7B%22source%22%3A%22game%22%2C%22source_id%22%3A' . $id . '%7D' : 'state&body=%7B%22source%22%3A%22game%22%7D'),
            'headers' => [
                'Cookie' => $this->KEY,
                'Referer' => 'https://h5.m.jd.com/babelDiy/Zeus/6yCQo2eDJPbyPXrC3eMCtMWZ9ey/index.html',
            ],
        ];
        $nobyda = new nobyda();
        $nobyda->get($this->GameUrl, function ($error, $response, $data) use ($nobyda, $stop, $id) {
            try {
                if ($error) {
                    throw new InternalServerErrorException(T($error));
                } else {
                    $Details = $this->LogDetails ? "response:\n" . $data : '';
                    $cc = json_decode($data, true);
                    if (!$id) {
                        $status = $merge['SpeedUp']['success'] ? "本次" : "";
                        DI()->logger->info("天天加速-查询" . $status . "任务中 " . $Details);
                    } else {
                        DI()->logger->info("天天加速-开始本次任务 " . $Details);
                    }
                    if ($cc['message'] == "not login") {
                        $merge['SpeedUp']['fail'] = 1;
                        $merge['SpeedUp']['notify'] = "京东天天-加速: 失败, 原因: Cookie失效‼️";
                        DI()->logger->info("\n天天加速-Cookie失效");
                    } else if ($cc['message'] == "success") {
                        if ($cc['data']['task_status'] == 0 && $cc['data']['source_id']) {
                            $taskID = $cc['data']['source_id'];
                            call_user_func([new JingDongSpeedUp,'main'],$stop, $taskID);
                        } else if ($cc['data']['task_status'] == 1) {
                            if (!$merge['SpeedUp']['notify']) $merge['SpeedUp']['fail'] = 1;
                            if (!$merge['SpeedUp']['notify']) $merge['SpeedUp']['notify'] = "京东天天-加速: 失败, 原因: 加速中 ⚠️";
                            $EndTime = $cc['data']['end_time'] ? $cc['data']['end_time'] : "";
                            DI()->logger->info("\n天天加速-目前结束时间: \n" . $EndTime);
                            $step1 = call_user_func([new JDQueryTask, 'main'], $stop);
                            $step2 = call_user_func([new JDReceiveTask, 'main'], $stop, $step1);
                            $step3 = call_user_func([new JDQueryTaskID, 'main'], $stop, $step2);
                            $step4 = call_user_func([new JDUseProps, 'main'], $stop, $step3);
                        } else if ($cc['data']['task_status'] == 2) {
                            if (preg_match('/\"beans_num\":\d+/', $data)) {
                                preg_match('/\"beans_num\":(\d+)/', $data, $matches);
                                $merge['SpeedUp']['notify'] = "京东天天-加速: 成功, 明细: " . $matches[1] . "京豆 🐶";
                                $merge['SpeedUp']['bean'] = $matches[1];
                            } else {
                                $merge['SpeedUp']['notify'] = "京东天天-加速: 成功, 明细: 无京豆 🐶";
                            }
                            $merge['SpeedUp']['success'] = 1;
                            DI()->logger->info("天天加速-领取上次奖励成功");
                            call_user_func([new JingDongSpeedUp, 'main'], $stop, false);
                        } else {
                            $merge['SpeedUp']['fail'] = 1;
                            $merge['SpeedUp']['notify'] = "京东天天-加速: 失败, 原因: 未知 ⚠️";
                            DI()->logger->info("天天加速-判断状态码失败");
                        }
                    } else {
                        $merge['SpeedUp']['fail'] = 1;
                        $merge['SpeedUp']['notify'] = "京东天天-加速: 失败, 原因: 未知 ⚠️";
                        DI()->logger->info("天天加速-判断状态失败");
                    }
                }
            } catch (\Exception $eor) {
                $nobyda->AnError('京东天天-加速', 'SpeedUp', $eor);
            } finally {
                return;
            }

        });
    }

}
