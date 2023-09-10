<?php

namespace App\Jobs;

use App\Models\Fund\FundNetValue;
use App\Models\Fund\FundValuation;
use App\Utils\Tools;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * 基金估值抓取任务
 */
class FundValuationCatchJob implements ShouldQueue
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
                $res = \App\Utils\Tools::curl(5)->setHeader([
                    'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A372 Safari/604.1',
                    'Referer' => 'https://fund.eastmoney.com/',
                ])->get("https://fundgz.1234567.com.cn/js/{$this->fundCode}.js");
                preg_match('/{[^}]*}/', $res, $matches);
                if (!empty($matches)) {
                    $data = \App\Utils\Tools::jsonDecode($matches[0]);
                    FundValuationUpdateJob::dispatch([
                        'code' => $data['fundcode'],
                        'valuation_time' => $data['gztime'],
                        'valuation_source' => 'https://fundgz.1234567.com.cn/js/{fundCode}.js',
                        'estimated_net_value' => $data['gsz'],
                    ]);
                }
            } catch (\Exception $exception) {
                Log::error($exception->getMessage());
            }
        }
    }
}
