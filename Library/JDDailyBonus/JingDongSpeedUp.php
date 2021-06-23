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
    /**
     * @var initial
     */
    private $initial; // 初始化参数

    public function __construct(initial $initial)
    {
        $this->initial = $initial;
    }

    public function main($stop = 0, $id = false)
    {
        usleep($stop * 1000);
        $GameUrl = [
            'url' => 'https://api.m.jd.com/?appid=memberTaskCenter&functionId=flyTask_' . ($id ? 'start&body=%7B%22source%22%3A%22game%22%2C%22source_id%22%3A' . $id . '%7D' : 'state&body=%7B%22source%22%3A%22game%22%7D'),
            'headers' => [
                'Cookie' => $this->initial->KEY,
                'Referer' => 'https://h5.m.jd.com/babelDiy/Zeus/6yCQo2eDJPbyPXrC3eMCtMWZ9ey/index.html',
            ],
        ];
        $this->initial->custom->get($GameUrl, function ($error, $response, $data) use ($stop, $id) {
            try {
                if ($error) {
                    throw new InternalServerErrorException(T($error));
                } else {
                    $Details = $this->initial->LogDetails ? "response:\n" . $data : '';
                    $cc = json_decode($data, true);
                    if (!$id) {
                        $status = $this->initial->merge->SpeedUp->success ? "本次" : "";
                        $this->initial->custom->log("天天加速-查询" . $status . "任务中 " . $Details);
                    } else {
                        $this->initial->custom->log("天天加速-开始本次任务 " . $Details);
                    }
                    if ($cc['message'] == "not login") {
                        $this->initial->merge->SpeedUp->fail = 1;
                        $this->initial->merge->SpeedUp->notify = "京东天天-加速: 失败, 原因: Cookie失效‼️";
                        $this->initial->custom->log("\n天天加速-Cookie失效");
                    } else if ($cc['message'] == "success") {
                        if ($cc['data']['task_status'] == 0 && $cc['data']['source_id']) {
                            $taskID = $cc['data']['source_id'];
                            call_user_func([new JingDongSpeedUp($this->initial), 'main'], $stop, $taskID);
                        } else if ($cc['data']['task_status'] == 1) {
                            if (!$this->initial->merge->SpeedUp->notify) $this->initial->merge->SpeedUp->fail = 1;
                            if (!$this->initial->merge->SpeedUp->notify) $this->initial->merge->SpeedUp->notify = "京东天天-加速: 失败, 原因: 加速中 ⚠️";
                            $EndTime = $cc['data']['end_time'] ? $cc['data']['end_time'] : "";
                            $this->initial->custom->log("天天加速-目前结束时间: " . $EndTime);
                            $step1 = call_user_func([new JDQueryTask($this->initial), 'main'], $stop);
                            $step2 = call_user_func([new JDReceiveTask($this->initial), 'main'], $stop, $step1);
                            $step3 = call_user_func([new JDQueryTaskID($this->initial), 'main'], $stop, $step2);
                            $step4 = call_user_func([new JDUseProps($this->initial), 'main'], $stop, $step3);
                        } else if ($cc['data']['task_status'] == 2) {
                            if (preg_match('/\"beans_num\":\d+/', $data)) {
                                preg_match('/\"beans_num\":(\d+)/', $data, $matches);
                                $this->initial->merge->SpeedUp->notify = "京东天天-加速: 成功, 明细: " . $matches[1] . "京豆 🐶";
                                $this->initial->merge->SpeedUp->bean = $matches[1];
                            } else {
                                $this->initial->merge->SpeedUp->notify = "京东天天-加速: 成功, 明细: 无京豆 🐶";
                            }
                            $this->initial->merge->SpeedUp->success = 1;
                            $this->initial->custom->log("天天加速-领取上次奖励成功");
                            call_user_func([new JingDongSpeedUp($this->initial), 'main'], $stop, false);
                        } else {
                            $this->initial->merge->SpeedUp->fail = 1;
                            $this->initial->merge->SpeedUp->notify = "京东天天-加速: 失败, 原因: 未知 ⚠️";
                            $this->initial->custom->log("天天加速-判断状态码失败");
                        }
                    } else {
                        $this->initial->merge->SpeedUp->fail = 1;
                        $this->initial->merge->SpeedUp->notify = "京东天天-加速: 失败, 原因: 未知 ⚠️";
                        $this->initial->custom->log("天天加速-判断状态失败");
                    }
                }
            } catch (\Exception $eor) {
                $this->initial->custom->AnError('京东天天-加速', 'SpeedUp', $eor);
            } finally {
                return;
            }

        });
    }

}
