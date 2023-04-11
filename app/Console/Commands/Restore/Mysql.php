<?php

namespace App\Console\Commands\Restore;

use App\Utils\Tools;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class Mysql extends Command
{
    protected $signature = 'restore:mysql';
    protected $description = '还原数据库';
    protected $config = [];

    public function __construct()
    {
        $this->config = config('database.connections.mysql');
        parent::__construct();
    }

    public function handle()
    {
        // gunzip < /backup/mysqldump/db_name.sql.gz | mysql -uroot -proot integralsystem --default-character-set=utf8
        $start_time = microtime(true);
        $backup_path = storage_path(implode(DIRECTORY_SEPARATOR, [
            'backup',
            'mysql',
            $this->config['database'],
        ]));
        // 查找备份目录下所有文件
        $files = array_filter(Tools::scanFile($backup_path), function ($file) {
            return strpos($file, '.sql.gz') !== false;
        });
        // $name = $this->anticipate('请输入要还原的文件名', $files);
        $file = $this->choice('请选择要还原的文件', $files);
        if (empty($file)) return 0;
        $backup_file = $backup_path . DIRECTORY_SEPARATOR . $file;
        if (!file_exists($backup_file)) return 0;
        // 读取框架默认数据库连接配置
        exec("gunzip < '{$backup_file}' | mysql -h{$this->config['host']} -P{$this->config['port']} -u{$this->config['username']} -p{$this->config['password']} {$this->config['database']} --default-character-set={$this->config['charset']}");
        // 指令输出
        $this->info('执行|还原数据库|耗时：' . ((microtime(true) - $start_time) * 1000) . ' 毫秒');
        $this->info('还原数据库完成！');
        return 0;
    }

}
