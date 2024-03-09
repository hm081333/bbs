<?php

namespace App\Console;

use App\Console\Commands\Fund\NetValueUpdateCommand;
use App\Console\Commands\Fund\ValuationUpdateCommand;
use App\Console\Commands\Fund\ValuationWriteCommand;
use App\Utils\Tools;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $now_time = Tools::now();
        // $schedule->command('inspire')->hourly();
        $schedule->command(NetValueUpdateCommand::class)
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
        $schedule->command(ValuationUpdateCommand::class, ['--dayfund'])
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
        $schedule->command(ValuationUpdateCommand::class, ['--eastmoney'])
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
        $schedule->command(ValuationWriteCommand::class)
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
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

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
