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
        $curl = curl();
        $type = 'yunda';
        $type = 'yuantong';
        $sn = '4316310678563';
        $sn = 'YT6099342207919';
        $url = "http://baidu.kuaidi100.com/query?type={$type}&postid={$sn}&id=4&temp=0." . rand(100000000000000, 999999999999999);
        /*$cookie = [
            '_adadqeqwe1321312dasddocTitle' => 'kuaidi100',
            '_adadqeqwe1321312dasddocReferrer' => '',
            '_adadqeqwe1321312dasddocHref' => '',
            '_clck' => 'dfkmcm|1|ewr|0',
            '_clsk' => 'm7j1ky|1637914607216|4|1|a.clarity.ms/collect',
            'WWWID' => 'WWW118D69EB0C45CD7E750841A6AE41C81D',
            'inputpostid' => '4316310678563',
            'comcode' => 'yunda',
            'beyond' => '%25E9%259F%25B5%25E8%25BE%25BE%252C4316310678563%252C',
            'beatles' => '1',
            'Hm_lvt_22ea01af58ba2be0fec7c11b25e88e6c' => '1637916074',
            'Hm_lpvt_22ea01af58ba2be0fec7c11b25e88e6c' => '1637916074',
        ];*/
        $header = [
            'Referer' => 'http://baidu.kuaidi100.com/',
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.45 Safari/537.36',
            // 'Cookie' => 'Hm_lpvt_22ea01af58ba2be0fec7c11b25e88e6c=' . time(),
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];
        $result = $curl->setCookie([
            'Hm_lpvt_22ea01af58ba2be0fec7c11b25e88e6c' => time(),
        ])->setHeader($header)->get($url);
        // var_dump(zlib_decode($result));
        var_dump($result);
        die;
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
