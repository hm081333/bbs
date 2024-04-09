<?php

namespace App\Jobs;

use App\Exceptions\Server\InternalServerErrorException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * 默认任务
 */
class DefaultJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int 任务调度时间
     */
    private int $dispatchTime;

    /**
     * @var object 待执行任务类
     */
    private object $jobClass;

    /**
     * @var array 扩展参数
     */
    private array $extendParams;


    /**
     * Create a new job instance.
     *
     * @param object     $jobClass     任务类
     * @param array|null $extendParams 任务追加参数
     *
     * @throws InternalServerErrorException
     */
    public function __construct(object $jobClass, ?array $extendParams)
    {
        $this->dispatchTime = time();
        if (!method_exists($jobClass, 'handle')) throw new InternalServerErrorException('待队列的类异常');
        $this->jobClass = $jobClass;
        $this->extendParams = $extendParams ?? [];
        $this->onConnection('redis');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        call_user_func_array([$this->jobClass, 'handle'], $this->extendParams);
    }
}
