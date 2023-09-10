<?php

namespace App\Jobs;

use App\Models\Fund\FundNetValue;
use App\Models\Fund\FundValuation;
use App\Utils\Tools;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * 基金估值抓取任务
 */
class FundNetValueCatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $fundCode;
    private string $catchType;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $fundCode, string $catchType)
    {
        $this->onQueue('fund');
        $this->onConnection('redis');
        $this->fundCode = $fundCode;
        $this->catchType = $catchType;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->catchType == 'sync-eastmoney') {
            try {
                $fund = Fund::where('code', $this->fundCode)->first();
                $allPages = 1;
                for ($page = 1; $page <= $allPages; $page++) {
                    $res = Tools::curl(5)
                        ->setHeader([
                            'Referer' => 'https://fundf10.eastmoney.com/',
                            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36',
                        ])
                        ->json_get('https://api.fund.eastmoney.com/f10/lsjz?fundCode=320007&pageIndex=1&pageSize=20', [
                            'fundCode' => $this->fundCode,
                            'pageIndex' => $page,
                            'pageSize' => 100,
                        ]);
                    // 根据返回数据，刷新分页总数
                    if ($allPages == 1) $allPages = ceil($res['TotalCount'] / $res['PageSize']);
                    // 是否存在历史净值数据列表
                    if (!empty($res['Data']) && !empty($res['Data']['LSJZList'])) {
                        // region 获取数据库已存在的净值时间
                        $first = $res['Data']['LSJZList'][0];
                        $last = $res['Data']['LSJZList'][count($res['Data']['LSJZList']) - 1];
                        /* @var $isset_net_value_time_arr Collection */
                        $isset_net_value_time_arr = FundNetValue::where('code', $this->fundCode)
                            ->where('net_value_time', '>=', Carbon::parse($last['FSRQ'])->timestamp)
                            ->where('net_value_time', '<=', Carbon::parse($first['FSRQ'])->timestamp)
                            ->pluck('net_value_time')
                            ->map(function ($item) {
                                return $item->format('Y-m-d');
                            })
                            ->toArray();
                        // endregion
                        $insert_list = [];
                        foreach ($res['Data']['LSJZList'] as $item) {
                            // 该时间净值已存在数据库，跳过
                            if (in_array($item['FSRQ'], $isset_net_value_time_arr)) continue;
                            $insert_list[] = [
                                'fund_id' => $fund->id,
                                'code' => $fund->code,
                                'name' => $fund->name,
                                'unit_net_value' => $item['DWJZ'],// 单位净值
                                'cumulative_net_value' => $item['LJJZ'],// 累计净值
                                'net_value_time' => Carbon::parse($item['FSRQ'])->timestamp,
                                'created_at' => Carbon::parse($item['FSRQ'])->setHour(15)->timestamp,
                                'updated_at' => time(),
                            ];
                        }
                        FundNetValue::insert($insert_list);
                    }
                }
            } catch (Exception $exception) {
                Log::error($exception->getMessage());
            }
        }
    }
}
