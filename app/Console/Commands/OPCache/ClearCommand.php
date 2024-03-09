<?php

namespace App\Console\Commands\OPCache;

use Illuminate\Console\Command;

class ClearCommand extends Command
{
    use CreatesRequest;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'opcache:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '清除OPCache';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $response = $this->sendRequest('clear');
        $response->throw();

        if ($response['result']) {
            $this->info('已清除OPCache');
        } else {
            $this->error('未配置OPCache');

            return 2;
        }
    }
}
