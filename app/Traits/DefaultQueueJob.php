<?php

namespace App\Traits;

trait DefaultQueueJob
{
    public function dispatch()
    {
        return \App\Jobs\DefaultJob::dispatch($this, func_get_args())->onQueue('default');
    }

    public function handle()
    {
    }
}
