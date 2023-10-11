<?php

namespace App\Jobs;

use App\Models\Fund\Fund;
use App\Models\Fund\FundNetValue;
use App\Utils\Tools;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * 基金净值更新任务
 */
class FundNetValueUpdateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int 任务推送时间
     */
    private int $dispatchTime;

    /**
     * @var array 净值数据
     */
    private array $fundNetValue;

    /**
     * @var string 基金代码
     */
    private string $fundCode;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->dispatchTime = time();
        $this->fundNetValue = $data;

        $this->fundCode = (string)$this->fundNetValue['code'];
        $this->fundNetValue['unit_net_value'] = $this->fundNetValue['unit_net_value'] ?: 0;
        $this->fundNetValue['cumulative_net_value'] = $this->fundNetValue['cumulative_net_value'] ?: 0;

        $this->onQueue('fund');
        $this->onConnection('redis');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (empty($this->fundCode)) $this->fundCode = (string)$this->fundNetValue['code'];
        // 基金净值更新逻辑
        /* @var $fund Fund */
        $fund = Fund::getByCode($this->fundCode);
        if ($fund) {
            /* @var $fund_net_value FundNetValue */
            $fund_net_value = FundNetValue::where([
                'fund_id' => $fund->id,
                'net_value_time' => Carbon::parse($this->fundNetValue['net_value_time'])->timestamp,
            ])->first();
            if (!$fund_net_value) {
                $fundValuation = FundNetValue::create([
                    'fund_id' => $fund->id,
                    'code' => $fund->code,
                    'name' => $fund->name,
                    'unit_net_value' => $this->fundNetValue['unit_net_value'],// 单位净值
                    'cumulative_net_value' => $this->fundNetValue['cumulative_net_value'],// 累计净值
                    'net_value_time' => Carbon::parse($this->fundNetValue['net_value_time'])->timestamp,
                    'created_at' => Carbon::parse($this->fundNetValue['net_value_time'])->setHour(15)->timestamp,
                    //'updated_at' => Tools::now(),
                ]);
//                \App\Events\FundNetValueUpdated::dispatch($fundValuation);
            } else if (
                Tools::math($fund_net_value->unit_net_value, '<>', $this->fundNetValue['unit_net_value'], 4)
                ||
                Tools::math($fund_net_value->cumulative_net_value, '<>', $this->fundNetValue['cumulative_net_value'], 4)
            ) {
                $fund_net_value->unit_net_value = $this->fundNetValue['unit_net_value'];// 单位净值
                $fund_net_value->cumulative_net_value = $this->fundNetValue['cumulative_net_value'];// 累计净值
                $fund_net_value->created_at = Carbon::parse($this->fundNetValue['net_value_time'])->setHour(15)->timestamp;
                //$fund_net_value->updated_at = Tools::now();
                $fund_net_value->save();
//                \App\Events\FundNetValueUpdated::dispatch($fund_net_value);
            }
        }
    }
}
