<?php
/**
 * 计划任务入口示例
 */

defined('API_ROOT') || define('API_ROOT', dirname(dirname(__FILE__)));
require_once API_ROOT . '/vendor/autoload.php';
require_once API_ROOT . '/public/init.php';// 引入核心文件

// $mq = new PhalApi\Task\MQ\RedisMQ(); //默认使用redis的MQ
// for ($i = 0; $i < 7; $i++) {
//     $mq->add('App.Site.Index', ['username' => 'lyiho' . $i]);
// }
// die;

try {
    $progress = new PhalApi\Task\Progress();
    $progress->run();
} catch (Exception $ex) {
    echo $ex->getMessage();
    echo "\n\n";
    echo $ex->getTraceAsString();
    // notify ...
}
