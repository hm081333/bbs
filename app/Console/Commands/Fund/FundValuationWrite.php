<?php

namespace App\Console\Commands\Fund;

use App\Jobs\FundNetValueUpdateJob;
use App\Jobs\FundUpdateJob;
use App\Jobs\FundValuationUpdateJob;
use App\Models\Fund\FundValuation;
use App\Utils\Juhe\Calendar;
use App\Utils\Tools;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class FundValuationWrite extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fund:valuation-write';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '基金：估值更新写入';

    private $once_write_count = 1000;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $global_start_time = microtime(true);
        $this->comment('写入基金估值');
        while (true) {
            $start_time = microtime(true);
            $inserts = Redis::spop('fund:valuation:wait-write', $this->once_write_count);
            $this->info('成功|集合中获取|' . count($inserts) . '|条基金估值|耗时：' . (microtime(true) - $start_time) . ' 秒');
            if (empty($inserts)) break;
            $start_time = microtime(true);
            try {
                FundValuation::insert(array_map(function ($value) {
                    return Tools::jsonDecode($value);
                }, $inserts));
                $this->info('成功|写入|' . count($inserts) . '|条基金估值|耗时：' . (microtime(true) - $start_time) . ' 秒');
            } catch (\Exception $e) {
                $this->info('失败|写入|' . count($inserts) . '|条基金估值|耗时：' . (microtime(true) - $start_time) . ' 秒');
                $this->error($e->getMessage());
                Redis::sadd('fund:valuation:wait-write', ...$inserts);
            }
        }
        $this->info('完成|写入基金估值|耗时：' . (microtime(true) - $global_start_time) . ' 秒');
        return Command::SUCCESS;
    }
}
