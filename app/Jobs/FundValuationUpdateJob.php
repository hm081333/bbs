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
use Illuminate\Support\Facades\Cache;

/**
 * 基金估值更新任务
 */
class FundValuationUpdateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var array 估值数据
     */
    private array $fundValuationData;

    /**
     * @var Carbon 估值时间
     */
    private Carbon $valuation_time;

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
        $this->onQueue('fund');
        $this->onConnection('redis');
        $this->fundValuationData = $data;

        $this->fundCode = (string)$this->fundValuationData['code'];
        if (!empty($this->fundValuationData['valuation_time'])) $this->valuation_time = $this->fundValuationData['valuation_time'] instanceof Carbon ? $this->fundValuationData['valuation_time'] : Carbon::parse($this->fundValuationData['valuation_time']);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (empty($this->fundCode)) $this->fundCode = (string)$this->fundValuationData['code'];
        if (empty($this->fundCode) || empty($this->valuation_time)) return;
        // 基金估值更新逻辑
        /* @var $fund Fund */
        $fund = Fund::getByCode($this->fundCode);
        if ($fund) {
            $fund_valuation_filter = [
                'fund_id' => $fund->id,
                'valuation_time' => $this->valuation_time->timestamp,
                'valuation_source' => $this->fundValuationData['valuation_source'],
            ];
            $fund_valuation_cache_key = base64_encode(Tools::jsonEncode($fund_valuation_filter));
            if (!FundValuation::getCacheOrSet($fund_valuation_cache_key, function () use ($fund_valuation_filter) {
                return FundValuation::where($fund_valuation_filter)->count('id');
            }, 3600)) {
                $insert_data = [
                    'fund_id' => $fund->id,
                    'code' => $fund->code,
                    'name' => $fund->name,
                    'unit_net_value' => 0,
                    // 最新预估净值
                    'estimated_net_value' => empty($this->fundValuationData['estimated_net_value']) ? null : $this->fundValuationData['estimated_net_value'],
                    'estimated_growth' => 0,
                    'estimated_growth_rate' => 0,
                    // 估值时间
                    'valuation_time' => $this->valuation_time->timestamp,
                    // 估值来源
                    'valuation_source' => $this->fundValuationData['valuation_source'],
                ];
                $insert_data['created_at'] = $insert_data['updated_at'] = time();
                // 计算增长和增长率
                if (empty($fund->unit_net_value)) {
                    /* @var $fundNetValue FundNetValue */
                    $fundNetValue = FundNetValue::where('code', $this->fundCode)
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
                // \App\Events\FundValuationUpdated::dispatch($fundValuation);
            }
            FundValuation::setCache($fund_valuation_cache_key, 1);
        }
    }
}
