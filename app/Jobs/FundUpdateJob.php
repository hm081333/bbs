<?php

namespace App\Jobs;

use App\Models\Fund\Fund;
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
            Fund::create([
                'code' => $this->fundData['code'],
                'name' => $this->fundData['name'],
                'pinyin_initial' => $this->fundData['pinyin_initial'],
                'type' => $this->fundData['type'],
                //'created_at' => Tools::now(),
                //'updated_at' => Tools::now(),
            ]);
        } else if (
            $fund->name != $this->fundData['name']
            ||
            $fund->pinyin_initial != $this->fundData['pinyin_initial']
            ||
            $fund->type != $this->fundData['type']
        ) {
            Log::channel('fund')->info("update fund|{$this->fundData['code']}|{$fund->name}:{$this->fundData['name']}|{$fund->pinyin_initial}:{$this->fundData['pinyin_initial']}|{$fund->type}:{$this->fundData['type']}");
            // 基金数据存在但数据不一致，更新
            $fund->name = $this->fundData['name'];
            $fund->pinyin_initial = $this->fundData['pinyin_initial'];
            $fund->type = $this->fundData['type'];
            //$fund->updated_at = Tools::now();
            $fund->save();
        }
    }
}
