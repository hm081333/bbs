<?php

namespace App\Console\Commands\Fund;

use App\Jobs\FundNetValueUpdateJob;
use App\Jobs\FundUpdateJob;
use App\Jobs\FundValuationUpdateJob;
use App\Models\Fund\FundValuation;
use App\Utils\Juhe\Calendar;
use App\Utils\Tools;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
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
    protected $signature = 'fund:valuation-write
    {--size : 集合元素数量}
    {--delete-exists : 删除已存在元素}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '基金：估值更新写入';

    /**
     * @var int 每次写入数量
     */
    private $once_write_count = 1000;

    /**
     * @var float|int 超时时间，避免定时器一直生成新进程运行，老进程不退出。单位秒。
     */
    private $timeout = 5 * 60;

    /**
     * @var string Redis集合键名
     */
    private $redis_key = 'fund:valuation:wait-write';
    private $failed_redis_key = 'fund:valuation:wait-write:failed';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $global_start_time = microtime(true);
        if ($this->option('size')) {
            $this->comment('获取估值集合元素数量');
            $this->info(Redis::scard($this->redis_key));
        } else if ($this->option('delete-exists')) {
            $this->comment('删除集合中重复估值');
            if (Redis::exists($this->failed_redis_key)) {
                while (!empty($failed_list = Redis::spop($this->failed_redis_key, $this->once_write_count))) {
                    $start_time = microtime(true);
                    try {
                        $inserts = array_filter($failed_list, function ($value) {
                            $value = Tools::jsonDecode($value);
                            $fund_valuation_set_key = 'fund:valuation:' . Tools::timeToCarbon($value['valuation_time'])->format('Ymd');
                            // 获取估值是否写入
                            $exists = FundValuation::where('fund_id', $value['fund_id'])
                                    ->where('valuation_time', $value['valuation_time'])
                                    ->where('valuation_source', $value['valuation_source'])
                                    ->count() > 0;
                            // 插入集合，标记为已写入
                            Redis::sadd($fund_valuation_set_key, $this->getFundValuationCacheKey($value));
                            if (Redis::ttl($fund_valuation_set_key) < 0) Redis::expireat($fund_valuation_set_key, Tools::today()->addDay()->timestamp);
                            return !$exists;
                        });
                        $count = count($inserts);
                        // 数据库不存在的估值写入到待写入集合
                        Redis::sadd($this->redis_key, ...$inserts);
                        $this->info('成功|写入|' . $count . '|条基金估值|耗时：' . (microtime(true) - $start_time) . ' 秒');
                    } catch (\Exception $e) {
                        $this->info('失败|写入|' . count($failed_list) . '|条基金估值|耗时：' . (microtime(true) - $start_time) . ' 秒');
                        $this->error($e->getMessage());
                        Redis::sadd($this->failed_redis_key, ...$failed_list);
                    }
                }
                // Redis::del($this->failed_redis_key);
            }
            $this->info('完成|写入基金估值|耗时：' . (microtime(true) - $global_start_time) . ' 秒');
        } else {
            $this->comment('写入基金估值');
            if (Redis::exists($this->redis_key)) {
                while (!empty($inserts = Redis::spop($this->redis_key, $this->once_write_count))) {
                    $start_time = microtime(true);
                    try {
                        FundValuation::insert(array_map(fn($value) => Tools::jsonDecode($value), $inserts));
                        $this->info('成功|写入|' . count($inserts) . '|条基金估值|耗时：' . (microtime(true) - $start_time) . ' 秒');
                    } catch (\Exception $e) {
                        $this->info('失败|写入|' . count($inserts) . '|条基金估值|耗时：' . (microtime(true) - $start_time) . ' 秒');
                        // $this->error($e->getMessage());
                        Redis::sadd($this->failed_redis_key, ...$inserts);
                    }
                    if ((microtime(true) - $global_start_time) >= $this->timeout) {
                        $this->info('进程运行超过' . $this->timeout . ' 秒，准备退出。');
                        break;
                    }
                }
            }
            $this->info('完成|写入基金估值|耗时：' . (microtime(true) - $global_start_time) . ' 秒');
        }
        return Command::SUCCESS;
    }

    /**
     * 获取估值在集合中的值
     * @param $fund_valuation
     * @return string
     */
    private function getFundValuationCacheKey($fund_valuation)
    {
        return base64_encode(Tools::jsonEncode([
            'fund_id' => $fund_valuation['fund_id'],
            'valuation_time' => Tools::timeToCarbon($fund_valuation['valuation_time'])->timestamp,
            'valuation_source' => $fund_valuation['valuation_source'],
        ]));
    }

}
