<?php

namespace App\Jobs;

use App\Models\Fund\Fund;
use App\Utils\Tools;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * 基金信息更新任务
 */
class FundUpdateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $fundData;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->onQueue('fund');
        $this->onConnection('redis');
        $this->fundData = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // 基金数据更新逻辑
        /* @var $fund Fund */
        $fund = Fund::where('code', $this->fundData['code'])->first();
        if (!$fund) {
            Log::channel('fund')->info("create fund|{$this->fundData['code']}|{$this->fundData['name']}|{$this->fundData['pinyin_initial']}|{$this->fundData['type']}");
            // 基金数据不存在，插入
            $insert_data = [
                'code' => $this->fundData['code'],
                'name' => $this->fundData['name'],
                'pinyin_initial' => $this->fundData['pinyin_initial'],
                'type' => $this->fundData['type'],
                //'created_at' => Tools::now(),
                //'updated_at' => Tools::now(),
            ];
            if (!empty($this->fundData['net_value_time'])) $insert_data['net_value_time'] = $this->fundData['net_value_time'];// 净值更新时间
            if (!empty($this->fundData['unit_net_value'])) $insert_data['unit_net_value'] = $this->fundData['unit_net_value'];// 单位净值
            if (!empty($this->fundData['cumulative_net_value'])) $insert_data['cumulative_net_value'] = $this->fundData['cumulative_net_value'];// 累计净值
            Fund::create($insert_data);
        } else if (
            $fund->name != $this->fundData['name']
            ||
            $fund->pinyin_initial != $this->fundData['pinyin_initial']
            ||
            (!empty($this->fundData['net_value_time']) && $fund->net_value_time != $this->fundData['net_value_time'])
//            ||
//            Tools::math($fund->unit_net_value, '<>', $this->fundData['unit_net_value'], 4)
//            ||
//            Tools::math($fund->cumulative_net_value, '<>', $this->fundData['cumulative_net_value'], 4)
        ) {
            Log::channel('fund')->info("update fund|{$this->fundData['code']}|{$fund->name}:{$this->fundData['name']}|{$fund->pinyin_initial}:{$this->fundData['pinyin_initial']}|{$fund->type}:{$this->fundData['type']}");
            // 基金数据存在但数据不一致，更新
            $fund->name = $this->fundData['name'];
            $fund->pinyin_initial = $this->fundData['pinyin_initial'];
            $fund->type = $this->fundData['type'];
            if (!empty($this->fundData['net_value_time'])) $fund->net_value_time = $this->fundData['net_value_time'];// 净值更新时间
            if (!empty($this->fundData['unit_net_value'])) $fund->unit_net_value = $this->fundData['unit_net_value'];// 单位净值
            if (!empty($this->fundData['cumulative_net_value'])) $fund->cumulative_net_value = $this->fundData['cumulative_net_value'];// 累计净值
            //$fund->updated_at = Tools::now();
            $fund->save();
        }
    }
}
