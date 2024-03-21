<?php

namespace App\Console\Commands\Fund;

use App\Jobs\FundNetValueCatchJob;
use App\Jobs\FundValuationCatchJob;
use App\Models\Fund\FundProduct;
use App\Utils\Juhe\Calendar;
use App\Utils\Tools;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class IterateCommand extends Command
{
    /**
     * The name and signature of the console command.
     * https://fund.eastmoney.com/
     * @var string
     */
    protected $signature = 'fund:iterate
    {--cache : 缓存所有基金}
    {--sync-eastmoney-valuation : 同步（天天基金网）估值}
    {--sync-eastmoney-net_value : 同步（天天基金网）历史净值}';

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
            if (!Tools::isOpenDoorDay($now_time) || !Tools::isOpenDoorTime($now_time)) {
                $this->comment('不在基金开门时间');
                return Command::SUCCESS;
            }
        }

        FundProduct::chunk(500, function (Collection $fund_list) {
            $fund_list->each(function (FundProduct $fund) {
                // 缓存所有基金
                if ($this->option('cache')) {
                    $this->info("刷新缓存｜{$fund->code}");
                    FundProduct::setCache($fund->code, $fund, null);
                    $this->info("缓存完成｜{$fund->code}");
                }
                // 同步（天天基金网）估值
                if ($this->option('sync-eastmoney-valuation')) {
                    FundValuationCatchJob::dispatch($fund->code, 'sync-eastmoney');
                }
                // 同步（天天基金网）历史净值
                if ($this->option('sync-eastmoney-net_value')) {
                    FundNetValueCatchJob::dispatch($fund->code, 'sync-eastmoney');
                }
            });
        });
        return Command::SUCCESS;
    }
}
