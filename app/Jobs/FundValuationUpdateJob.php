<?php

namespace App\Jobs;

use App\Models\Fund;
use App\Models\FundNetValue;
use App\Models\FundValuation;
use App\Utils\Tools;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
                'valuation_time' => Carbon::parse($this->fundValuationData['valuation_time']),
                'valuation_source' => $this->fundValuationData['valuation_source'],
            ])->first();
            if (!$fund_valuation) {
                /* @var $last_net_value FundNetValue */
                $last_net_value = FundNetValue::where('fund_id', $fund->id)->orderByDesc('unit_net_value')->first();
                if ($last_net_value) {
                    $estimated_growth = Tools::math($this->fundValuationData['estimated_net_value'], '-', $last_net_value->unit_net_value, 4);
                    $estimated_growth_rate = Tools::math($estimated_growth, '/', $last_net_value->unit_net_value, 10);
                    $estimated_growth_percent = Tools::math($estimated_growth_rate, '*', '100', 4);
                } else {
                    $estimated_growth = 0;
                    $estimated_growth_percent = 0;
                }
                FundValuation::create([
                    'fund_id' => $fund->id,
                    'code' => $fund->code,
                    'name' => $fund->name,
                    'unit_net_value' => $last_net_value ? $last_net_value->unit_net_value : 0,// 单位净值
                    'estimated_net_value' => $this->fundValuationData['estimated_net_value'],// 预估净值
                    'estimated_growth' => $estimated_growth,// 预估增长值
                    'estimated_growth_rate' => $estimated_growth_percent,// 预估增长率
                    'valuation_time' => Carbon::parse($this->fundValuationData['valuation_time']),
                    'valuation_source' => $this->fundValuationData['valuation_source'],
                ]);
            }

        }
    }
}
