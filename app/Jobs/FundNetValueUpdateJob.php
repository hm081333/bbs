<?php

namespace App\Jobs;

use App\Models\Fund;
use App\Models\FundNetValue;
use App\Utils\Tools;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FundNetValueUpdateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $fundNetValue;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->onQueue('fund');
        $this->onConnection('redis');
        $this->fundNetValue = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // 基金净值更新逻辑
        /* @var $fund Fund */
        $fund = Fund::where('code', $this->fundNetValue['code'])->first();
        if ($fund) {
            /* @var $fund_net_value FundNetValue */
            $fund_net_value = FundNetValue::where([
                'fund_id' => $fund->id,
                'net_value_time' => Carbon::parse($this->fundNetValue['net_value_time']),
            ])->first();
            if (!$fund_net_value) {
                FundNetValue::create([
                    'fund_id' => $fund->id,
                    'code' => $fund->code,
                    'name' => $fund->name,
                    'unit_net_value' => $this->fundNetValue['unit_net_value'],// 单位净值
                    'cumulative_net_value' => $this->fundNetValue['cumulative_net_value'],// 累计净值
                    'net_value_time' => Carbon::parse($this->fundNetValue['net_value_time']),
                    'created_at' => Carbon::parse($this->fundNetValue['net_value_time'])->setHour(15),
                    'updated_at' => Tools::now(),
                ]);
            } else if (
                $fund_net_value->unit_net_value != $this->fundNetValue['unit_net_value']
                ||
                $fund_net_value->cumulative_net_value != $this->fundNetValue['cumulative_net_value']
            ) {
                $fund_net_value->unit_net_value = $this->fundNetValue['unit_net_value'];// 单位净值
                $fund_net_value->cumulative_net_value = $this->fundNetValue['cumulative_net_value'];// 累计净值
                $fund_net_value->created_at = Carbon::parse($this->fundNetValue['net_value_time'])->setHour(15);
                $fund_net_value->updated_at = Tools::now();
                $fund_net_value->save();
            }
        }
    }
}
