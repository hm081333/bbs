<?php

namespace Library\JDDailyBonus;

use Library\Exception\InternalServerErrorException;
use function Common\DI;
use function PhalApi\T;

/**
 * 通知模块
 * Class notify
 * @package Library\JDDailyBonus
 */
class notify
{
    /**
     * @var initial
     */
    private $initial; // 初始化参数

    public function __construct(initial $initial)
    {
        $this->initial = $initial;
    }

    public function main($stop = 0)
    {
        try {
            $bean = 0;
            $steel = 0;
            $success = 0;
            $fail = 0;
            $err = 0;
            $notify = '';
            foreach ($this->initial->merge as $merge) {
                $bean += $merge['bean'];
                $steel += $merge['steel'];
                $success += $merge['success'];
                $fail += $merge['fail'];
                $err += $merge['error'];
                $notify .= $merge['notify'] ? "\n" . $merge['notify'] : "";
            }
            $Cash = $this->initial->merge['JDCash']['TCash'] ? $this->initial->merge['JDCash']['TCash'] . "红包" : "";
            $Steel = $this->initial->merge['JRSteel']['TSteel'] ? $this->initial->merge['JRSteel']['TSteel'] . "钢镚" . ($Cash ? ", " : "") : "";
            $beans = $this->initial->merge['JDShake']['Qbear'] ? $this->initial->merge['JDShake']['Qbear'] . "京豆" . ($Steel || $Cash ? ", " : "") : "";
            $bsc = $beans ? "\n" : $Steel ? "\n" : $Cash ? "\n" : "获取失败\n";
            $Tbean = $bean ? $bean . "京豆" . ($steel || $this->initial->merge['JDCash']['Cash'] ? ", " : "") : "";
            $TSteel = $steel ? $steel . "钢镚" . ($this->initial->merge['JDCash']['Cash'] ? ", " : "") : "";
            $TCash = $this->initial->merge['JDCash']['Cash'] ? $this->initial->merge['JDCash']['Cash'] . "红包" : "";
            $Tbsc = $Tbean ? "\n" : $TSteel ? "\n" : $TCash ? "\n" : "获取失败\n";
            $Ts = $success ? "成功" . $success . "个" . ($fail || $err ? ", " : "") : "";
            $Tf = $fail ? "失败" . $fail . "个" . ($err ? ", " : "") : "";
            $Te = $err ? "错误" . $err . "个\n" : $success ? "\n" : $fail ? "\n" : "获取失败\n";
            $one = "【签到概览】:  " . $Ts . $Tf . $Te;
            $two = "【签到总计】:  " . $Tbean . $TSteel . $TCash . $Tbsc;
            $three = "【账号总计】:  " . $beans . $Steel . $Cash . $bsc;
            // $four = "【左滑 '查看' 以显示签到详情】\n";
            $four = "";
            $disa = $this->initial->custom->disable ? "\n检测到上次执行意外崩溃, 已为您自动禁用相关接口. 如需开启请前往BoxJs ‼️‼️\n" : "";
            $DName = $this->initial->merge['JDShake']['nickname'] ? $this->initial->merge['JDShake']['nickname'] : "获取失败";
            $Name = "【签到号一】:  " . $DName . "\n";
            // $this->initial->custom->log("\n" . $Name . $one . $two . $three . $four . $disa . $notify);
            $this->initial->custom->notify("", "", "\n" . $Name . $one . $two . $three . $four . $disa . $notify);
            $this->initial->custom->time();
            $this->initial->custom->done();
        } catch (\Exception $eor) {
            $this->initial->custom->notify("通知模块 " . $eor['name'] . "‼️", json_encode($eor), $eor['message']);
        } finally {
            return;
        }
    }

}
