<?php

namespace Library\JDDailyBonus;

use Library\Exception\InternalServerErrorException;
use function Common\DI;
use function PhalApi\T;

/**
 * 会员签到活动
 * Class JDUserSignPre
 * @package Library\JDDailyBonus
 */
class JDUserSignPre
{
    /**
     * @var initial
     */
    private $initial; // 初始化参数

    public function __construct(initial $initial)
    {
        $this->initial = $initial;
    }

    public function main($stop = 0, $key = false, $title = false, $ask = false)
    {
        $this->main1($stop, $key, $title, $ask);
    }

    public function main1($stop = 0, $key = false, $title = false, $ask = false)
    {
        usleep($stop * 1000);
        $JDUrl = [
            'url' => 'https://api.m.jd.com/?client=wh5&functionId=qryH5BabelFloors',
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Cookie' => $this->initial->KEY,
            ],
            'body' => 'body=' . urlencode('{"activityId":"' . $this->initial->acData[$key] . '"' . ($ask ? ',"paginationParam":"2",' . $ask : '')),
        ];
        $this->initial->custom->post($JDUrl, function ($error, $response, $data) use ($stop, $key, $title, $ask) {
            try {
                if ($error) {
                    throw new InternalServerErrorException(T($error));
                } else {
                    preg_match('/\"turnTableId\":\"(\d+)\"/', $data, $turnTableId);
                    preg_match('/\"paginationFlrs\":\"\[\[.+?\]\]\"/', $data, $page);
                    if (preg_match('/enActK/', $data)) { // 含有签到活动数据
                        $od = json_decode($data, true);
                        $params = $od['floatLayerList'] ?? [];
                        $params = array_filter($params, function ($o) {
                            return !empty($o['params']) && preg_match('/enActK/', $o['params']);
                        });
                        $params = array_map(function ($o) {
                            return $o['params'];
                        }, $params);
                        $params = array_pop($params);
                        if (!$params) { // 第一处找到签到所需数据
                            // floatLayerList未找到签到所需数据，从floorList中查找
                            $signInfo = $od['floorList'] ?? [];
                            $signInfo = array_filter($signInfo, function ($o) {
                                return !empty($o['template']) && $o['template'] == 'signIn' && !empty($o['signInfos']) && !empty($o['signInfos']['params']) && preg_match('/enActK/', $o['signInfos']['params']);
                            });
                            $signInfo = array_map(function ($o) {
                                return $o['signInfos'];
                            }, $signInfo);
                            $signInfo = array_pop($signInfo);
                            if ($signInfo) {
                                if ($signInfo['signStat'] == 1) {
                                    $this->initial->custom->log("{$title}重复签到");
                                    $this->initial->merge[$key]['notify'] = "{$title}: 失败, 原因: 已签过 ⚠️";
                                    $this->initial->merge[$key]['fail'] = 1;
                                } else {
                                    $params = $signInfo['params'];
                                }
                            } else {
                                $this->initial->merge[$key]['notify'] = "{$title}: 失败, 活动查找异常 ⚠️";
                                $this->initial->merge[$key]['fail'] = 1;
                            }
                        }
                        if ($params) {
                            // 执行签到处理
                            $data = [
                                'params' => $params,
                            ];
                            $this->then($data, $stop, $key, $title, $ask);
                            return $data;
                        }
                    } else if ($turnTableId) { // 无签到数据, 但含有关注店铺签到
                        $boxds = $this->initial->custom->read("JD_Follow_disable") === "false" ? false : true;
                        if ($boxds) {
                            $this->initial->custom->log("{$title}关注店铺");
                            $data = intval($turnTableId[1]);
                            $this->then($data, $stop, $key, $title, $ask);
                            return $data;
                        } else {
                            $this->initial->merge[$key]['notify'] = "{$title}: 失败, 需要关注店铺 ⚠️";
                            $this->initial->merge[$key]['fail'] = 1;
                        }
                    } else if ($page && !$ask) { // 无签到数据, 尝试带参查询
                        $boxds = $this->initial->custom->read("JD_Retry_disable") === "false" ? false : true;
                        if ($boxds) {
                            $this->initial->custom->log("{$title}二次查询");
                            $data = $page[0];
                            $this->then($data, $stop, $key, $title, $ask);
                            return $data;
                        } else {
                            $this->initial->merge[$key]['notify'] = "{$title}: 失败, 请尝试开启增强 ⚠️";
                            $this->initial->merge[$key]['fail'] = 1;
                        }
                    } else {
                        $this->initial->merge[$key]['notify'] = "{$title}: 失败, " . (!$data ? '需要手动执行' : '不含活动数据') . ' ⚠️';
                        $this->initial->merge[$key]['fail'] = 1;
                    }
                }
            } catch (\Exception $eor) {
                $this->initial->custom->AnError($title, $key, $eor);
            }
        });
    }

    public function main2($stop = 0, $key = false, $title = false)
    {
        usleep($stop * 1000);
        $JDUrl = [
            'url' => "https://pro.m.jd.com/mall/active/{$this->initial->acData[$key]}/index.html",
            'headers' => [
                'Cookie' => $this->initial->KEY,
            ],
        ];
        $this->initial->custom->get($JDUrl, function ($error, $response, $data) use ($stop, $key, $title) {
            try {
                if ($error) {
                    throw new InternalServerErrorException(T($error));
                } else {
                    preg_match('/\"params\":\"\{\\\"enActK.+?\\\"\}\"/', $data, $act);
                    preg_match('/\"turnTableId\":\"(\d+)\"/', $data, $turnTable);
                    preg_match('/\"paginationFlrs\":\"\[\[.+?\]\]\"/', $data, $page);
                    if ($act) { // 含有签到活动数据
                        return $act;
                    } else if ($turnTable) { // 无签到数据, 但含有关注店铺签到
                        $boxds = $this->initial->custom->read("JD_Follow_disable") === "false" ? false : true;
                        if ($boxds) {
                            $this->initial->custom->log("{$title}关注店铺");
                            return intval($turnTable[1]);
                        } else {
                            $this->initial->merge[$key]['notify'] = "{$title}: 失败, 需要关注店铺 ⚠️";
                            $this->initial->merge[$key]['fail'] = 1;
                        }
                    } else if ($page) { // 无签到数据, 尝试带参查询
                        $boxds = $this->initial->custom->read("JD_Retry_disable") === "false" ? false : true;
                        if ($boxds) {
                            $this->initial->custom->log("{$title}二次查询");
                            return $page[0];
                        } else {
                            $this->initial->merge[$key]['notify'] = "{$title}: 失败, 请尝试开启增强 ⚠️";
                            $this->initial->merge[$key]['fail'] = 1;
                        }
                    } else {
                        $this->initial->merge[$key]['notify'] = "{$title}: 失败, " . (!$data ? '需要手动执行' : '不含活动数据') . ' ⚠️';
                        $this->initial->merge[$key]['fail'] = 1;
                    }
                }
            } catch (\Exception $eor) {
                $this->initial->custom->AnError($title, $key, $eor);
            }
        });
    }

    public function then($data, $stop = 0, $key = false, $title = false, $ask = false)
    {
        if (is_array($data)) return call_user_func([new JDUserSign($this->initial), 'main1'], $stop, $key, $title, urlencode(json_encode($data)));
        if (is_numeric($data)) return call_user_func([new JDUserSign($this->initial), 'main2'], $stop, $key, $title, $data);
        if (is_string($data)) return call_user_func([new JDUserSignPre($this->initial), 'main1'], $stop, $key, $title, $data);
    }

}