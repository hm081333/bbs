<?php

namespace App\Jobs;

use App\Models\Fund\Fund;
use App\Utils\Tools;
use Carbon\Carbon;
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

    /**
     * @var int 任务推送时间
     */
    private int $dispatchTime;

    /**
     * @var array 基金数据
     */
    private array $fundData;

    /**
     * @var string 基金代码
     */
    private string $fundCode;

    /**
     * @var Carbon 基金净值时间
     */
    private Carbon $net_value_time;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->dispatchTime = time();
        $this->fundData = $data;

        $this->fundCode = (string)$this->fundData['code'];
        if (!empty($this->fundData['net_value_time'])) $this->net_value_time = Tools::timeToCarbon($this->fundData['net_value_time']);
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
        // 基金数据更新逻辑
        /* @var $fund Fund */
        $fund = Fund::getByCode($this->fundCode);
        if (!$fund) {
            Log::channel('fund')->info("create fund|{$this->fundCode}|{$this->fundData['name']}|{$this->fundData['pinyin_initial']}|{$this->fundData['type']}");
            // 基金数据不存在，插入
            $insert_data = [
                'code' => $this->fundCode,
                'name' => $this->fundData['name'],
                'pinyin_initial' => $this->fundData['pinyin_initial'],
                'type' => $this->fundData['type'],
                //'created_at' => Tools::now(),
                //'updated_at' => Tools::now(),
            ];
            if (!empty($this->net_value_time)) {
                $insert_data['net_value_time'] = $this->net_value_time;// 净值更新时间
                if (!empty($this->fundData['unit_net_value'])) $insert_data['unit_net_value'] = $this->fundData['unit_net_value'];// 单位净值
                if (!empty($this->fundData['cumulative_net_value'])) $insert_data['cumulative_net_value'] = $this->fundData['cumulative_net_value'];// 累计净值
            }
            $fund = Fund::create($insert_data);
            // 基金信息写入缓存，方便后续使用
            Fund::setCache($this->fundCode, $fund, null);
        } else if (
            $fund->name != $this->fundData['name']
            ||
            $fund->pinyin_initial != $this->fundData['pinyin_initial']
            ||
            (!empty($this->net_value_time) && $this->net_value_time->ne($fund->net_value_time))
//            ||
//            Tools::math($fund->unit_net_value, '<>', $this->fundData['unit_net_value'], 4)
//            ||
//            Tools::math($fund->cumulative_net_value, '<>', $this->fundData['cumulative_net_value'], 4)
        ) {
            Log::channel('fund')->info("update fund|{$this->fundCode}|{$fund->name}:{$this->fundData['name']}|{$fund->pinyin_initial}:{$this->fundData['pinyin_initial']}|{$fund->type}:{$this->fundData['type']}|{$fund->net_value_time->copy()->format('Y-m-d')}:{$this->fundData['net_value_time']}|{$fund->unit_net_value}:{$this->fundData['unit_net_value']}|{$fund->cumulative_net_value}:{$this->fundData['cumulative_net_value']}");
            // 基金数据存在但数据不一致，更新
            $fund->name = $this->fundData['name'];
            $fund->pinyin_initial = $this->fundData['pinyin_initial'];
            $fund->type = $this->fundData['type'];
            // 基金数据存在但净值时间不一致，更新
            if (!empty($this->net_value_time)) {
                $fund->net_value_time = $this->net_value_time;// 净值更新时间
                if (!empty($this->fundData['unit_net_value'])) $fund->unit_net_value = $this->fundData['unit_net_value'];// 单位净值
                if (!empty($this->fundData['cumulative_net_value'])) $fund->cumulative_net_value = $this->fundData['cumulative_net_value'];// 累计净值
            }
            //$fund->updated_at = Tools::now();
            $fund->save();
            // 基金信息写入缓存，方便后续使用
            Fund::setCache($this->fundCode, $fund, null);
        }
    }
}
