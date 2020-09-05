<?php

namespace Library\JDDailyBonus;

use Exception;
use Library\Exception\BadRequestException;
use Library\Exception\InternalServerErrorException;
use function Common\DI;
use function PhalApi\T;

/**
 * 会员签到活动 签到
 * Class JDUserSign
 * @package Library\JDDailyBonus
 */
class JDUserSign
{
    /**
     * @var initial
     */
    private $initial; // 初始化参数

    public function __construct(initial $initial)
    {
        $this->initial = $initial;
    }

    public function main($stop = 0, $key = false, $title = false, $body = false)
    {
        throw new BadRequestException(T('错误调用函数'));
    }

    public function main1($stop = 0, $key = false, $title = false, $body = false)
    {
        usleep($stop * 1000);
        $JDUrl = [
            'url' => 'https://api.m.jd.com/client.action?functionId=userSign',
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Cookie' => $this->initial->KEY,
            ],
            'body' => "body={$body}&client=wh5",
        ];
        $this->initial->custom->post($JDUrl, function ($error, $response, $data) use ($stop, $key, $title, $body) {
            try {
                if ($error) {
                    throw new InternalServerErrorException(T($error));
                } else {
                    $Details = $this->initial->LogDetails ? "response:\n" . $data : '';
                    $cc = json_decode($data);
                    if (preg_match('/签到成功/', $data)) {
                        $this->initial->custom->log("{$title}签到成功(1){$Details}");
                        if (preg_match('/(\"text\":\"\d+京豆\")/', $data)) {
                            preg_match('/\d+/', $cc['awardList'][0]['text'], $beanQuantity);
                            var_dump($beanQuantity);
                            die;
                            $this->initial->merge[$key]['notify'] = "{$title}: 成功, 明细: {$beanQuantity}京豆 🐶";
                            $this->initial->merge[$key]['bean'] = $beanQuantity;
                            $this->initial->merge[$key]['success'] = 1;
                        } else {
                            $this->initial->merge[$key]['notify'] = "{$title}: 成功, 明细: 无京豆 🐶";
                            $this->initial->merge[$key]['success'] = 1;
                        }
                    } else {
                        $this->initial->custom->log("{$title}签到失败(1){$Details}");
                        if (preg_match('/(已签到|已领取)/', $data)) {
                            $this->initial->merge[$key]['notify'] = "{$title}: 失败, 原因: 已签过 ⚠️";
                        } else if (preg_match('/(不存在|已结束|未开始)/', $data)) {
                            $this->initial->merge[$key]['notify'] = "{$title}: 失败, 原因: 活动已结束 ⚠️";
                        } else if ($cc['code'] == 3) {
                            $this->initial->merge[$key]['notify'] = "{$title}: 失败, 原因: Cookie失效‼️";
                        } else {
                            $this->initial->merge[$key]['notify'] = "{$title}: 失败, 原因: 未知 ⚠️";
                        }
                        $this->initial->merge[$key]['fail'] = 1;
                    }
                }
            } catch (Exception $eor) {
                $this->initial->custom->AnError($title, $key, $eor);
            } finally {
                return;
            }

        });
    }

    public function main2($stop = 0, $key = false, $title = false, $tid = false)
    {
        $this->initial->custom->get([
            'url' => "https://jdjoy.jd.com/api/turncard/channel/detail?turnTableId={$tid}",
            'headers' => [
                'Cookie' => $this->initial->KEY,
            ],
        ], function ($error, $response, $data) {
        });

        usleep($stop * 1000);
        $JDUrl = [
            'url' => 'https://jdjoy.jd.com/api/turncard/channel/sign',
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Cookie' => $this->initial->KEY,
            ],
            'body' => "turnTableId={$tid}",
        ];
        $this->initial->custom->post($JDUrl, function ($error, $response, $data) use ($stop, $key, $title, $tid) {
            try {
                if ($error) {
                    throw new InternalServerErrorException(T($error));
                } else {
                    $Details = $this->initial->LogDetails ? "response:\n" . $data : '';
                    $cc = json_decode($data);
                    if ($cc['success'] == true) {
                        $this->initial->custom->log("{$title}签到成功(2){$Details}");
                        if (preg_match('/\"jdBeanQuantity\":\d+/', $data)) {
                            $this->initial->merge[$key]['notify'] = "{$title}: 成功, 明细: {$cc['data']['jdBeanQuantity']}京豆 🐶";
                            $this->initial->merge[$key]['bean'] = $cc['data']['jdBeanQuantity'];
                        } else {
                            $this->initial->merge[$key]['notify'] = "{$title}: 成功, 明细: 无京豆 🐶";
                        }
                        $this->initial->merge[$key]['success'] = 1;
                    } else {
                        $this->initial->custom->log("{$title}签到失败(2){$Details}");
                        if (preg_match('/(已经签到|已经领取)/', $data)) {
                            $this->initial->merge[$key]['notify'] = "{$title}: 失败, 原因: 已签过 ⚠️";
                        } else if (preg_match('/(不存在|已结束|未开始)/', $data)) {
                            $this->initial->merge[$key]['notify'] = "{$title}: 失败, 原因: 活动已结束 ⚠️";
                        } else if (preg_match('/(没有登录|B0001)/', $data)) {
                            $this->initial->merge[$key]['notify'] = "{$title}: 失败, 原因: Cookie失效‼️";
                        } else {
                            $this->initial->merge[$key]['notify'] = "{$title}: 失败, 原因: 未知 ⚠️";
                        }
                        $this->initial->merge[$key]['fail'] = 1;
                    }
                }
            } catch (Exception $eor) {
                $this->initial->custom->AnError($title, $key, $eor);
            } finally {
                return;
            }

        });
    }

}
