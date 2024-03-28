<?php

namespace App\Console\Commands\Task;

use App\Utils\TieBa\Misc;
use App\Utils\Tools;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TiebaCommand extends Command
{
    protected $signature = 'task:tieba
    {type : 任务类型（sign:贴吧签到|retry:贴吧签到重试|send_info:推送签到详情信息）}';

    protected $description = '贴吧签到相关任务';

    private $job_start_time;

    public function handle()
    {
        $this->job_start_time = microtime(true);
        switch ($this->argument('type')) {
            case 'sign':// 签到
                Log::info('执行定时:贴吧签到');
                Misc::doSignAll();// 签到所有贴吧
                break;
            case 'retry':// 重试
                Log::info('执行定时:贴吧重试签到');
                Misc::doSignAll(true);// 重试所有出错贴吧
                break;
            case 'send_detail':
                Log::info('推送签到详情信息');
                Misc::sendTieBaSignDetailAll();
                break;
        }
        $this->info('完成|' . $this->description . '|耗时：' . Tools::secondToTimeText(microtime(true) - $this->job_start_time));
        return Command::SUCCESS;
    }
}
