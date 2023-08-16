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
        $fund = Fund::where('code', $this->fundNetValue['code'])->first();
        if ($fund) {
            \App\Models\FundNetValue::updateOrCreate([
                'fund_id' => $fund->id,
                'net_value_time' => Carbon::parse($this->fundNetValue['net_value_time']),
            ], [
                'code' => $fund->code,
                'name' => $fund->name,
                'unit_net_value' => $this->fundNetValue['unit_net_value'],// 单位净值
                'cumulative_net_value' => $this->fundNetValue['cumulative_net_value'],// 累计净值
                'created_at' => Carbon::parse($this->fundNetValue['net_value_time'])->setHour(15),
                //'updated_at' => $net_value_time,
            ]);
        }
    }
}
