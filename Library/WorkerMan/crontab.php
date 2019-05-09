<?php
/**
 * 定时任务
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2019/05/09
 * Time: 9:42
 */
defined('API_ROOT') || define('API_ROOT', dirname(dirname(dirname(__FILE__))));

require_once API_ROOT . '/public/init.php';

use \Workerman\Worker;
use \Workerman\Lib\Timer;

$task = new Worker();
// 开启多少个进程运行定时任务，注意业务是否在多进程有并发问题
$task->count = 1;
// 进程任务名称
$task->name = 'crontab';
// 30 */1 * * * php /var/www/html/bbs/Library/task.php -a sign >/dev/null 2>&1
// 分钟(0-59) 小时(0-23) 日期(1-31) 月份(1-12) 星期几(0-7) 命令/脚本
$task->onWorkerStart = function ($task) {
    // 每1秒执行一次
    Timer::add(1, function () {
        var_dump(1);die;
    });
};

// 如果不是全局启动，则运行runAll方法
if (!defined('GLOBAL_START')) {
    Worker::runAll();
}
