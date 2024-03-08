<?php

namespace App\Console\Commands\Backup;

use App\Utils\Tools;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class Mysql extends Command
{
    protected $signature = 'backup:mysql';
    protected $description = '备份数据库';
    protected $config = [];

    public function __construct()
    {
        // 读取框架默认数据库连接配置
        $this->config = config('database.connections.mysql');
        parent::__construct();
    }

    public function handle()
    {
        // mysqldump -uroot -proot integralsystem --default-character-set=utf8mb4 | gzip > /backup/mysqldump/db_name.sql.gz
        $start_time = microtime(true);
        $backup_path = storage_path(implode(DIRECTORY_SEPARATOR, [
            'backup',
            'mysql',
            $this->config['database'],
        ]));
        // 创建备份目录
        Tools::createDir($backup_path);
        $backup_file = $backup_path . DIRECTORY_SEPARATOR . date('Y-m-d H_i_s') . '.sql.gz';
        // 执行备份命令
        exec("mysqldump -h{$this->config['host']} -P{$this->config['port']} -u{$this->config['username']} -p{$this->config['password']} {$this->config['database']} --default-character-set={$this->config['charset']} | gzip > '$backup_file'");
        // 文件删除时间（保存6个月）
        // $delete_time = strtotime(date('Y-m-d') . ' -6 month');
        $delete_time = Tools::today()->subMonths(6);
        // 查找备份目录下所有文件
        $files = Tools::scanFile($backup_path);
        foreach ($files as $file) {
            // 移除前后缀与连接符
            $file_time = str_replace(['.sql.gz', '-', '_', ' '], '', $file);
            // $file_time = strtotime($file_time);
            // 无法解析时间，文件名异常，不进行删除
            // if (!$file_time) continue;
            $file_time = Tools::timeToCarbon($file_time);
            if (!$file_time) continue;
            // 文件时间在6个月以前
            if ($file_time->lte($delete_time)) {
                $file_path = $backup_path . $file;
                Log::info('删除过期数据库备份|' . $file_path);
                @unlink($file_path);
            }
        }
        // 指令输出
        $this->info('执行|备份数据库|耗时：' . ((microtime(true) - $start_time) * 1000) . ' 毫秒');
        $this->info('备份数据库完成！');
        return Command::SUCCESS;
    }

}
