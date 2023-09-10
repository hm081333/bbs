<?php

namespace App\Console\Commands\Fund;

use App\Jobs\FundActionJob;
use App\Jobs\FundNetValueCatchJob;
use App\Jobs\FundNetValueUpdateJob;
use App\Jobs\FundUpdateJob;
use App\Jobs\FundValuationCatchJob;
use App\Jobs\FundValuationUpdateJob;
use App\Models\Fund\Fund;
use App\Utils\Tools;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class FundIterate extends Command
{
    /**
     * The name and signature of the console command.
     * https://fund.eastmoney.com/
     * @var string
     */
    protected $signature = 'fund:iterate
    {--sync-eastmoney-valuation : 同步（天天基金网）估值}';

    /**
     * The console command description.
     * https://www.1234567.com.cn/
     * @var string
     */
    protected $description = '基金：遍历执行';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->option('sync-eastmoney-valuation')) {
            //$this->comment('获取基金估值列表');
            $now_time = Tools::now();
            if ($now_time->lt(date('Y-m-d 9:25')) || ($now_time->gt(date('Y-m-d 11:35')) && $now_time->lt(date('Y-m-d 12:55'))) || $now_time->gt(date('Y-m-d 15:05'))) {
                $this->comment('不在基金开门时间');
                return Command::SUCCESS;
            }
        }

        $offset = 0;
        $limit = 100;
        while (true) {
            $fund_codes = Fund::offset($offset)->limit($limit)->select(['id', 'code'])->pluck('code');
            if ($fund_codes->isEmpty()) break;
            $offset += $limit;
            foreach ($fund_codes as $fund_code) {
                // 同步（天天基金网）估值
                if ($this->option('sync-eastmoney-valuation')) {
                    FundValuationCatchJob::dispatch($fund_code, 'sync-eastmoney');
                }
                // 同步（天天基金网）历史净值
                if ($this->option('sync-eastmoney-net_value')) {
                    FundNetValueCatchJob::dispatch($fund_code, 'sync-eastmoney');
                }
            }
        }
        return Command::SUCCESS;
    }
}
