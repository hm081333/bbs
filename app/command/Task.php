<?php
declare (strict_types=1);

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

class Task extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('task')
            ->setDescription('the task command');
    }


    protected function execute(Input $input, Output $output)
    {
        // php think make:controller
        // php think make:model
        $makes = [];
        $bbs_dir = scanFile('/Users/lyiho/Documents/code/php/bbs/src');
        foreach ($bbs_dir as $app => $item) {
            foreach ($item['Api'] as $api_file_name) {
                if (is_array($api_file_name) || strpos($api_file_name, '.php') === false) continue;
                $api = str_replace('.php', '', $api_file_name);
                $makes["php think make:controller {$app}@{$api}"] = false;
                $makes["php think make:validate {$app}@{$api}"] = false;
            }
            foreach ($item['Domain'] as $domain_file_name) {
                if (is_array($domain_file_name) || strpos($domain_file_name, '.php') === false) continue;
                $domain = str_replace('.php', '', $domain_file_name);
                $makes["php think make:controller {$app}@{$domain}"] = false;
                $makes["php think make:validate {$app}@{$domain}"] = false;
            }
            foreach ($item['Model'] as $model_file_name) {
                if (is_array($model_file_name) || strpos($model_file_name, '.php') === false) continue;
                $model = str_replace('.php', '', $model_file_name);
                $makes["php think make:model {$model}"] = false;
            }
            // var_dump($item);
            // die;
        }
        foreach ($makes as $cmd => $res) {
            exec($cmd, $out);
            var_dump($out);
        }
        die;
        // exec('docker-compose up -d', $out);
        // 指令输出
        $output->writeln('task');
    }
}
