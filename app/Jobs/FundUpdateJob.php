<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FundUpdateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $fundData;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->onQueue('fund');
        $this->onConnection('redis');
        $this->fundData = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \App\Models\Fund::updateOrCreate([
            'code' => $this->fundData['code'],
        ], [
            'name' => $this->fundData['name'],
            'pinyin_initial' => $this->fundData['pinyin_initial'],
            'type' => $this->fundData['type'],
        ]);
    }
}
