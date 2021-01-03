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
            $cash = 0;
            $money = 0;
            $subsidy = 0;
            $success = 0;
            $fail = 0;
            $err = 0;
            $notify = '';
            foreach ($this->initial->merge as $merge) {
                $bean += $merge->bean ?: 0;
                $steel += $merge->steel ?: 0;
                $cash += $merge->cash ?: 0;
                $money += $merge->money ?: 0;
                $subsidy += $merge->subsidy ?: 0;
                $success += $merge->success ?: 0;
                $fail += $merge->fail ?: 0;
                $err += $merge->error ?: 0;
                $notify .= $merge->notify ? "\n" . $merge->notify : "";
            }

            /*

            $nobyda.notify("", "", Name + one + two + three + four + five + disa + notify, {
                'media-url': $nobyda.headUrl || 'https://cdn.jsdelivr.net/gh/Orz-3/task@master/jd.png'
            });
            $nobyda.headUrl = null;
            if ($nobyda.isJSBox) {
                if (add && DualAccount) {
                    Shortcut = Name + one + two + three + "\n"
                } else if (!add && DualAccount) {
                    $intents.finish(Name + one + two + three + four + five + notify)
                } else if (typeof Shortcut != "undefined") {
                    $intents.finish(Shortcut + Name + one + two + three)
                }
            }
            if (DualAccount) {
                double();
            } else {
                $nobyda.time();
                $nobyda.done();
            }
            */

            $Cash = $this->initial->merge->TotalCash && $this->initial->merge->TotalCash->TCash ? $this->initial->merge->TotalCash->TCash . "红包" : "";
            $Steel = $this->initial->merge->TotalSteel && $this->initial->merge->TotalSteel->TSteel ? $this->initial->merge->TotalSteel->TSteel . "钢镚" : "";
            $beans = $this->initial->merge->TotalBean && $this->initial->merge->TotalBean->Qbear ? $this->initial->merge->TotalBean->Qbear . "京豆" . ($Steel ? ", " : "") : "";
            $Money = $this->initial->merge->TotalMoney && $this->initial->merge->TotalMoney->TMoney ? $this->initial->merge->TotalMoney->TMoney . "现金" . ($Cash ? ", " : "") : "";
            $Subsidy = $this->initial->merge->TotalSubsidy && $this->initial->merge->TotalSubsidy->TSubsidy ? $this->initial->merge->TotalSubsidy->TSubsidy . "金贴" . ($Money || $Cash ? ", " : "") : "";
            // $bsc = $beans ? "\n" : ($Steel ? "\n" : ($Cash ? "\n" : "获取失败\n"));
            $Tbean = $bean ? $bean . "京豆" . ($steel ? ", " : "") : "";
            $TSteel = $steel ? $steel . "钢镚" : "";
            $TCash = $cash ? $cash . "红包" . ($subsidy || $money ? ", " : "") : "";
            $TSubsidy = $subsidy ? $subsidy . "金贴" . ($money ? ", " : "") : "";
            $TMoney = $money ? $money . "现金" : "";
            // $Tbsc = $Tbean ? "\n" : $TSteel ? "\n" : $TCash ? "\n" : "获取失败\n";
            $Ts = $success ? "成功" . $success . "个" . ($fail || $err ? ", " : "") : "";
            $Tf = $fail ? "失败" . $fail . "个" . ($err ? ", " : "") : "";
            $Te = $err ? "错误" . $err . "个\n" : "";

            $one = "【签到概览】:  " . $Ts . $Tf . $Te . ($Ts || $Tf || $Te ? "\n" : "获取失败\n");
            $two = "【签到总计】:  " . ($Tbean || $TSteel ? '【签到奖励】:  ' . $Tbean . $TSteel . "\n" : '');
            $three = "【账号总计】:  " . ($TCash || $TSubsidy || $TMoney ? '【其他奖励】:  ' . $TCash . $TSubsidy . $TMoney . "\n" : '');
            $four = "【账号总计】:  " . $beans . $Steel . ($beans || $Steel ? "\n" : "获取失败\n");
            $five = "【其他总计】:  " . $Subsidy . $Money . $Cash . ($Subsidy || $Money || $Cash ? "\n" : "获取失败\n");
            $DName = $this->initial->merge->TotalBean && $this->initial->merge->TotalBean->nickname ? $this->initial->merge->TotalBean->nickname : "获取失败";
            $Name = "【签到号一】:  " . $DName . "\n";
            $disables = '';
            $amount = 0;
            $disa = !$notify || $amount ? "【温馨提示】:  检测到上次执行意外崩溃, 已禁用" . ($notify ? $amount . "个" : "所有") . "接口, 如需开启请前往BoxJs或查看脚本内第99行注释.\n" : "";
            $disa = '';
            $this->initial->custom->notify("", "", $Name . $one . $two . $three . $four . $five . $disa . $notify);
            $this->initial->custom->time();
            $this->initial->custom->done();
        } catch (\Exception $eor) {
            $this->initial->custom->notify("通知模块 " . $eor['name'] . "‼️", json_encode($eor), $eor['message']);
        } finally {
            return;
        }
    }

}
