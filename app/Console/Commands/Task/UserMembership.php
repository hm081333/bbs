<?php

namespace App\Console\Commands\Task;

use App\Utils\Tools;
use Illuminate\Console\Command;

class UserMembership extends Command
{
    protected $signature = 'task:user-membership';

    protected $description = '定时任务：会员身份处理';

    public function handle()
    {
        $today_begin = Tools::today();
        \App\Models\User::withTrashed()
            ->where('membership', '>', 0)
            ->whereNotNull('membership_exp_time')
            ->where('membership_exp_time', '<', $today_begin)
            ->update([
                'membership' => 0,
                'membership_exp_time' => null,
            ]);

        // 指令输出
        $this->info('任务完成！');
        return 0;
    }

}
