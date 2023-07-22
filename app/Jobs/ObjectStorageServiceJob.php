<?php

namespace App\Jobs;

use App\Utils\Tools;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ObjectStorageServiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string 相对路径
     */
    private $relative_path;
    /**
     * @var string 文件真实路径
     */
    private $file_path;

    /**
     * @param string $relative_path 相对路径
     * @param string $file_path 本地文件路径
     */
    public function __construct(string $relative_path, string $file_path)
    {
        $this->relative_path = $relative_path;
        $this->file_path = $file_path;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (empty(config('app.storage_url', ''))) return;
        switch (config('oss.default')) {
            case 'aliyun':
                Tools::aliyun_oss()->uploadFile($this->relative_path, $this->file_path);
                break;
            default:
                break;
        }
    }
}
