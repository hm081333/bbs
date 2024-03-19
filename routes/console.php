<?php

use App\Exceptions\Server\InternalServerErrorException;
use App\Jobs\FundValuationUpdateJob;
use App\Models\Fund\FundProduct;
use App\Models\Fund\FundNetValue;
use App\Models\Fund\FundValuation;
use App\Utils\Tools;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('testa', function () {
    $global_start_time = microtime(true);
    while (true) {
        $start_time = microtime(true);
        $failed_jobs = \Illuminate\Support\Facades\DB::table('failed_jobs_bak')->limit(500)->orderBy('id')->get()->map(fn($item) => (array)$item);
        $this->info('完成|查询|耗时：' . Tools::secondToTimeText(microtime(true) - $start_time));
        $this->info('select-' . $failed_jobs->count());
        if ($failed_jobs->count() <= 0) break;
        $res = \Illuminate\Support\Facades\DB::table('failed_jobs')->insert($failed_jobs->toArray());
        $this->info('完成|插入|耗时：' . Tools::secondToTimeText(microtime(true) - $start_time));
        $this->info('insert-' . $res);
        $exitCode = Artisan::call('queue:retry all');
        $this->info('完成|重试|耗时：' . Tools::secondToTimeText(microtime(true) - $start_time));
        $this->info('commandExitCode-' . $exitCode);
        if ($res && $exitCode === 0) {
            $res = \Illuminate\Support\Facades\DB::table('failed_jobs_bak')->whereIn('id', $failed_jobs->pluck('id'))->delete();
            $this->info('完成|删除|耗时：' . Tools::secondToTimeText(microtime(true) - $start_time));
            $this->info('delete-' . $res);
        }
    }
    $this->info('结束|耗时：' . Tools::secondToTimeText(microtime(true) - $global_start_time));

    // $this->call('queue:retry all');
})->purpose('cesi');
