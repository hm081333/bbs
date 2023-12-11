<?php

namespace App\Console;

use App\Console\Commands\Fund\FundNetValueUpdate;
use App\Console\Commands\Fund\FundValuationUpdate;
use App\Console\Commands\Fund\FundValuationWrite;
use App\Utils\Juhe\Calendar;
use App\Utils\Tools;
use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $now_time = Tools::now();
        // $schedule->command('inspire')->hourly();
        $schedule->command(FundNetValueUpdate::class)
            ->when(fn() => Tools::isOpenDoorDay($now_time))
            ->between('18:00', '24:00')
            ->everyThirtyMinutes()
            ->onOneServer()
            // 后台运行
            ->runInBackground()
            ->sendOutputTo(Tools::logsPath('FundNetValueUpdate/eastmoney/' . $now_time->format('Y-m-d')) . '/' . $now_time->format('H-i-s') . '.log')
            ->before(function () use ($now_time) {
                Tools::logsPath('FundNetValueUpdate/eastmoney/' . $now_time->format('Y-m-d'), true);
                // 任务即将执行。。。
                // Log::debug('FundNetValueUpdate run before');
            })
            ->after(function () {
                // 任务已经执行。。。
                // Log::debug('FundNetValueUpdate run after');
            });
        $schedule->command(FundValuationUpdate::class, ['--dayfund'])
            ->when(fn() => Tools::isOpenDoorDay($now_time) && Tools::isOpenDoorTime($now_time))
            ->everyFiveMinutes()
            ->onOneServer()
            // 后台运行
            ->runInBackground()
            ->sendOutputTo(Tools::logsPath('FundValuationUpdate/dayfund/' . $now_time->format('Y-m-d')) . '/' . $now_time->format('H-i-s') . '.log')
            ->before(function () use ($now_time) {
                Tools::logsPath('FundValuationUpdate/dayfund/' . $now_time->format('Y-m-d'), true);
                // 任务即将执行。。。
                // Log::debug('FundValuationUpdate --dayfund run before');
            })
            ->after(function () {
                // 任务已经执行。。。
                // Log::debug('FundValuationUpdate --dayfund run after');
            });
        $schedule->command(FundValuationUpdate::class, ['--eastmoney'])
            ->when(fn() => Tools::isOpenDoorDay($now_time) && Tools::isOpenDoorTime($now_time))
            ->everyFiveMinutes()
            ->onOneServer()
            // 后台运行
            ->runInBackground()
            ->sendOutputTo(Tools::logsPath('FundValuationUpdate/eastmoney/' . $now_time->format('Y-m-d')) . '/' . $now_time->format('H-i-s') . '.log')
            ->before(function () use ($now_time) {
                Tools::logsPath('FundValuationUpdate/eastmoney/' . $now_time->format('Y-m-d'), true);
                // 任务即将执行。。。
                // Log::debug('FundValuationUpdate --eastmoney run before');
            })
            ->after(function () {
                // 任务已经执行。。。
                // Log::debug('FundValuationUpdate --eastmoney run after');
            });
        $schedule->command(FundValuationWrite::class)
            ->when(fn() => Tools::isOpenDoorDay($now_time) && Tools::isOpenDoorTime($now_time))
            ->everyMinute()
            ->onOneServer()
            // 避免重复运行
            ->withoutOverlapping()
            // 后台运行
            ->runInBackground()
            ->before(function () {
                // 任务即将执行。。。
                // Log::debug('FundValuationWrite run before');
            })
            ->after(function () {
                // 任务已经执行。。。
                // Log::debug('FundValuationWrite run after');
            });
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }

    /**
     * 获取计划事件默认使用的时区。
     *
     * @return \DateTimeZone|string|null
     */
    protected function scheduleTimezone()
    {
        return 'Asia/Shanghai';
    }

}
