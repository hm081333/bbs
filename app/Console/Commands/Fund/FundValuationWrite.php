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
            if (Redis::exists($this->redis_key)) {
                $fund_valuation_set_key = 'fund:valuation:' . Tools::now()->format('Ymd');
                Redis::del($fund_valuation_set_key);
                FundValuation::where('valuation_time', '>=', Tools::now()->startOfDay()->timestamp)
                    ->where('valuation_time', '<=', Tools::now()->endOfDay()->timestamp)
                    ->select(['id', 'fund_id', 'valuation_time', 'valuation_source'])
                    ->chunk(5000, function (Collection $fund_valuation_list) use ($fund_valuation_set_key) {
                        $fund_valuation_list->each(function (FundValuation $fund_valuation) use ($fund_valuation_set_key) {
                            $fund_valuation_cache_key = base64_encode(Tools::jsonEncode([
                                'fund_id' => $fund_valuation->fund_id,
                                'valuation_time' => $fund_valuation->valuation_time->timestamp,
                                'valuation_source' => $fund_valuation->valuation_source,
                            ]));
                            $this->info($fund_valuation->id);
                            Redis::sadd($fund_valuation_set_key, $fund_valuation_cache_key);
                            if (Redis::ttl($fund_valuation_set_key) < 0) Redis::expireat($fund_valuation_set_key, Tools::today()->addDay()->timestamp);
                        });
                    });
                Redis::rename($this->redis_key, $this->redis_key . ':bak');
                while (true) {
                    $start_time = microtime(true);
                    $inserts = Redis::spop($this->redis_key . ':bak', $this->once_write_count);
                    $this->info('成功|集合中获取|' . count($inserts) . '|条基金估值|耗时：' . (microtime(true) - $start_time) . ' 秒');
                    if (empty($inserts)) break;
                    $start_time = microtime(true);
                    try {
                        $inserts = array_filter($inserts, function ($value) use ($fund_valuation_set_key) {
                            $value = Tools::jsonDecode($value);
                            $fund_valuation_cache_key = base64_encode(Tools::jsonEncode([
                                'fund_id' => $value['fund_id'],
                                'valuation_time' => $value['valuation_time'],
                                'valuation_source' => $value['valuation_source'],
                            ]));
                            $exists = Redis::sismember($fund_valuation_set_key, $fund_valuation_cache_key);
                            Redis::sadd($fund_valuation_set_key, $fund_valuation_cache_key);
                            return !$exists;
                        });
                        // 不存在的估值写入到另一集合
                        Redis::sadd($this->redis_key, ...$inserts);
                        $this->info('成功|写入|' . count($inserts) . '|条基金估值|耗时：' . (microtime(true) - $start_time) . ' 秒');
                    } catch (\Exception $e) {
                        $this->info('失败|写入|' . count($inserts) . '|条基金估值|耗时：' . (microtime(true) - $start_time) . ' 秒');
                        $this->error($e->getMessage());
                        Redis::sadd($this->redis_key . ':bak', ...$inserts);
                    }
                }
                Redis::del($this->redis_key . ':bak');
            }
            $this->info('完成|写入基金估值|耗时：' . (microtime(true) - $global_start_time) . ' 秒');
        } else {
            $this->comment('写入基金估值');
            if (Redis::exists($this->redis_key)) {
                while (!empty($inserts = Redis::spop($this->redis_key, $this->once_write_count))) {
                    $start_time = microtime(true);
                    $this->info('成功|集合中获取|' . count($inserts) . '|条基金估值|耗时：' . (microtime(true) - $start_time) . ' 秒');
                    if (empty($inserts)) break;
                    $start_time = microtime(true);
                    try {
                        FundValuation::insert(array_map(fn($value) => Tools::jsonDecode($value), $inserts));
                        $this->info('成功|写入|' . count($inserts) . '|条基金估值|耗时：' . (microtime(true) - $start_time) . ' 秒');
                    } catch (\Exception $e) {
                        $this->info('失败|写入|' . count($inserts) . '|条基金估值|耗时：' . (microtime(true) - $start_time) . ' 秒');
                        // $this->error($e->getMessage());
                        Redis::sadd($this->redis_key, ...$inserts);
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
}
