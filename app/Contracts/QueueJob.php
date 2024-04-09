<?php

namespace App\Contracts;

interface QueueJob
{
    /**
     * 队列任务调度
     *
     * @return mixed
     */
    public function dispatch();

    /**
     * 任务执行
     *
     * @return mixed
     */
    public function handle();
}
