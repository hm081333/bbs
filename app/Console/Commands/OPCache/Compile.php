<?php

namespace App\Console\Commands\OPCache;

use Illuminate\Console\Command;

class Compile extends Command
{
    use CreatesRequest;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'opcache:compile {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '预编译应用程序代码';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->line('编译脚本...');

        $response = $this->sendRequest('compile', ['force' => $this->option('force') ?? false]);
        $response->throw();

        if (isset($response['result']['message'])) {
            $this->warn($response['result']['message']);

            return 1;
        } else if ($response['result']) {
            // var_dump($response['result']);
            $this->info(sprintf('文件总数：%s，编译成功：%s', $response['result']['total_files_count'], $response['result']['compiled_count']));
        } else {
            $this->error('未配置OPCache');

            return 2;
        }
    }
}
