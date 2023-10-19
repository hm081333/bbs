<?php

namespace App\Console\Commands\Queue;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class Size extends Command
{
    protected $signature = 'queue:size';

    protected $description = '查看队列中任务数量';

    public function handle()
    {
        $queue = App::make('queue.connection');
        $size = $queue->size();
        // 指令输出
        $this->info($size);
        return 0;
    }

}
