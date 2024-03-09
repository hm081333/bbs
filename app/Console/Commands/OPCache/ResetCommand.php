<?php

namespace App\Console\Commands\OPCache;

use App\Utils\Tools;
use Illuminate\Console\Command;

class ResetCommand extends Command
{
    use CreatesRequest;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'opcache:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '重置OPCache';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        var_dump(Tools::scanFile(base_path()));
        die;
        $response = $this->sendRequest('reset');
        $response->throw();

        if ($response['result']) {
            $this->info('已重置OPCache');
        } else {
            $this->error('未配置OPCache');

            return 2;
        }
    }
}
