<?php

namespace App\Jobs;

use App\Models\Fund\Fund;
use App\Models\Fund\FundNetValue;
use App\Models\Fund\FundValuation;
use App\Utils\Tools;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * 基金估值更新任务
 */
class FundValuationUpdateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $fundValuationData;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->onQueue('fund');
        $this->onConnection('redis');
        $this->fundValuationData = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // 基金估值更新逻辑
        /* @var $fund Fund */
        $fund = Fund::where('code', $this->fundValuationData['code'])->first();
        if ($fund) {
            /* @var $fund_valuation FundValuation */
            $fund_valuation = FundValuation::where([
                'fund_id' => $fund->id,
                'valuation_time' => Carbon::parse($this->fundValuationData['valuation_time'])->timestamp,
                'valuation_source' => $this->fundValuationData['valuation_source'],
            ])->first();
            if (!$fund_valuation) {
                dump(Carbon::parse($this->fundValuationData['valuation_time'])->timestamp);
                $insert_data = [
                    'fund_id' => $fund->id,
                    'code' => $fund->code,
                    'name' => $fund->name,
                    'unit_net_value' => 0,
                    'estimated_net_value' => empty($this->fundValuationData['estimated_net_value']) ? null : $this->fundValuationData['estimated_net_value'],
                    'estimated_growth' => 0,
                    'estimated_growth_rate' => 0,
                    'valuation_time' => Carbon::parse($this->fundValuationData['valuation_time'])->timestamp,
                    'valuation_source' => $this->fundValuationData['valuation_source'],
                ];
                // 计算增长和增长率
                if (empty($fund->unit_net_value)) {
                    /* @var $fundNetValue FundNetValue */
                    $fundNetValue = FundNetValue::where('code', $this->fundValuationData['code'])
                        ->orderByDesc('net_value_time')
                        ->first();
                    if ($fundNetValue) {
                        $fund->net_value_time = $fundNetValue->net_value_time;// 净值更新时间
                        $fund->unit_net_value = $fundNetValue->unit_net_value;// 单位净值
                        $fund->cumulative_net_value = $fundNetValue->cumulative_net_value;// 累计净值
                        $fund->save();
                    }
                }
                if (!empty($fund->unit_net_value)) {
                    $insert_data['unit_net_value'] = $fund->unit_net_value;
                    $insert_data['estimated_growth'] = Tools::math($this->fundValuationData['estimated_net_value'], '-', $insert_data['unit_net_value'], 4);
                    $estimated_growth_rate = Tools::math($insert_data['estimated_growth'], '/', $insert_data['unit_net_value'], 10);
                    $insert_data['estimated_growth_rate'] = Tools::math($estimated_growth_rate, '*', '100', 4);
                }
                $fundValuation = FundValuation::create($insert_data);
                \App\Events\FundValuationUpdated::dispatch($fundValuation);
            }

        }
    }
}
