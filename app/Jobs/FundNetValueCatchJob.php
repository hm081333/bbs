<?php

namespace App\Jobs;

use App\Models\Fund\Fund;
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

    /**
     * @var int 任务推送时间
     */
    private int $dispatchTime;

    /**
     * @var string 基金代码
     */
    private string $fundCode;

    /**
     * @var string 抓取方式
     */
    private string $catchType;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $fundCode, string $catchType)
    {
        $this->dispatchTime = time();
        $this->fundCode = $fundCode;
        $this->catchType = $catchType;
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
        if ($this->catchType == 'sync-eastmoney') {
            try {
                /* @var $fund Fund */
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
                            'pageSize' => 500,
                        ]);
                    // 根据返回数据，刷新分页总数
                    if ($allPages == 1) $allPages = ceil($res['TotalCount'] / $res['PageSize']);
                    // 是否存在历史净值数据列表
                    if (!empty($res['Data']) && !empty($res['Data']['LSJZList'])) {
                        // region 获取数据库已存在的净值时间
                        $first = $res['Data']['LSJZList'][0];
                        $last = $res['Data']['LSJZList'][count($res['Data']['LSJZList']) - 1];
                        /* @var $isset_net_value_time_arr array */
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
                            $insert = [
                                'fund_id' => $fund->id,
                                'code' => $fund->code,
                                'name' => $fund->name,
                                'unit_net_value' => $item['DWJZ'],// 单位净值
                                'cumulative_net_value' => empty($item['LJJZ']) ? null : $item['LJJZ'],// 累计净值
                                'net_value_time' => Carbon::parse($item['FSRQ'])->timestamp,
                                'created_at' => Carbon::parse($item['FSRQ'])->setHour(15)->timestamp,
                                'updated_at' => time(),
                            ];
                            // 该行数据没有单位净值，跳过
                            if (empty($item['DWJZ'])) {
                                file_put_contents(storage_path('logs/non-unit_net_value.log'), Tools::jsonEncode([
                                        'catch' => $item,
                                        'insert' => $insert,
                                    ]) . PHP_EOL, FILE_APPEND);// 追加写入
                                continue;
                            }
                            // 该行数据没有累计净值
                            if (empty($item['LJJZ'])) file_put_contents(storage_path('logs/non-cumulative_net_value.log'), Tools::jsonEncode([
                                    'catch' => $item,
                                    'insert' => $insert,
                                ]) . PHP_EOL, FILE_APPEND);// 追加写入
                            $insert_list[] = $insert;
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
